// auth.js

/**
 * Required External Modules
 */
const express = require("express");
const router = express.Router();
const passport = require("passport");
const db = require('../db');
const querystring = require("querystring");
const reporterService = require('../services/reporter');
const snapshotService = require('../services/snapshots');
const helper = require('../helper');

require("dotenv").config();

/**
 * Routes Definitions
 */
router.get(
  "/login",
  passport.authenticate("auth0", {
    scope: "openid email profile"
  }),
  (req, res) => {
    res.redirect("/me");
  }
);

router.get("/callback", (req, res, next) => {
  passport.authenticate("auth0", (err, user, info) => {
    if (err) {
      return next(err);
    }
    if (!user) {
      return res.redirect("/login");
    }
    req.logIn(user, (err) => {
      if (err) {
        return next(err);
      }
      const returnTo = req.session.returnTo;
      delete req.session.returnTo;
      res.redirect(returnTo || "/");
    });
  })(req, res, next);
});

router.get("/logout", (req, res) => {
  req.logOut();

  // req.protocol is always http because of the reverse proxy, and 'trust proxy' is not working for some reason,
  // so hardcode https for production.
  let returnTo = (process.env.NODE_ENV === "production" ? "https" : "http") + "://" + req.hostname;
  const port = req.connection.localPort;

  if (port !== undefined && port !== 80 && port !== 443) {
    returnTo =
      process.env.NODE_ENV === "production"
        ? `${returnTo}/`
        : `${returnTo}:${port}/`;
  }

  const logoutURL = new URL(
    `https://${process.env.AUTH0_DOMAIN}/v2/logout`
  );

  const searchString = querystring.stringify({
    client_id: process.env.AUTH0_CLIENT_ID,
    returnTo: returnTo
  });
  logoutURL.search = searchString;
  res.redirect(logoutURL);
});


router.get("/me", helper.secured, async function(req, res, next){
  let [rows, fields] = await db.query(
    'SELECT id, name FROM `reporter` WHERE email = ?', req.user._json.email
  );
  const reporter = helper.first(rows);
  res.render("reporter", { reporter });
});


router.get("/reporters", helper.secured, async function(req, res, next){
  let [rows, fields] = await db.query('SELECT id, name FROM `reporter`');
  rows = helper.rows(rows);
  res.render('reporters', { reporters: rows });
}) 


router.get("/reporters/:reporterId", helper.secured, async function(req, res, next){
  const reporter = await reporterService.getSingle(parseInt(req.params.reporterId));
  res.render("reporter", { reporter });
})


router.get("/reporters/:reporterId/snapshots", async function(req, res, next){
  const reporter = await reporterService.getSingle(parseInt(req.params.reporterId));
  const snapshots = await snapshotService.forReporter(reporter, req.user, req.query.page);
  res.json({ snapshots });
});


router.delete("/reporters/:reporterId", async function(req, res, next){
  let reporterId = parseInt(req.params.reporterId);
  const [error, success] = await reporterService.del(reporterId);
  res.json({ success, error });
})


/**
 * Module Exports
 */
module.exports = router;
