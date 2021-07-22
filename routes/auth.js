// auth.js

/**
 * Required External Modules
 */
const express = require("express");
const router = express.Router();
const passport = require("passport");
const querystring = require("querystring");
const reporterService = require('../services/reporters');
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

  console.log(req.secure)
  let returnTo = (req.secure ? 'https' : 'http') + "://" + req.hostname;
  console.log(returnTo)
  const port = req.connection.localPort;
  
  req.logOut();

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

  console.log(logoutURL.search)
  res.redirect(logoutURL);
});


router.get("/me", helper.secured, async function(req, res, next){
  const { _raw, _json, ...userProfile } = req.user;
  const reporter = await reporterService.getSingle(_json.email);
  let snapshots = {};
  if(reporter){
    snapshots = await snapshotService.forReporter(reporter.id);
  }
  res.render("user", {
    userProfile: userProfile,
    reporter: reporter, 
    snapshots: snapshots
  });
});


/**
 * Module Exports
 */
module.exports = router;
