/**
 * Required External Modules
 */
const createError = require('http-errors');
const express = require('express');
const path = require('path');
const expressSession = require("express-session");
const passport = require("passport");
const Auth0Strategy = require("passport-auth0");
const logger = require('morgan');

require("dotenv").config(); // loads the vars from the .env file into process.env

/**
 * Required Internal Modules
 */
const db = require('./db');
const authRouter = require("./routes/auth");
const indexRouter = require('./routes/index');
const locationsRouter = require('./routes/locations');
const reportersRouter = require('./routes/reporters');
const buoysRouter = require('./routes/buoys');

/**
 * App Variables
 */
const app = express();
// const port = process.env.PORT || "3000";

/**
 * Session Configuration (New!)
 */
const session = {
  secret: process.env.SESSION_SECRET,
  cookie: {},
  resave: false,
  saveUninitialized: false
};

if(app.get("env") === "production"){
  // Serve secure cookies, requires HTTPS
  session.cookie.secure = true;
}

/**
 * Passport Configuration (New!)
 */
const strategy = new Auth0Strategy(
  {
    domain: process.env.AUTH0_DOMAIN,
    clientID: process.env.AUTH0_CLIENT_ID,
    clientSecret: process.env.AUTH0_CLIENT_SECRET,
    callbackURL: process.env.AUTH0_CALLBACK_URL
  },
  function(accessToken, refreshToken, extraParams, profile, done) {
    /**
     * Access tokens are used to authorize users to an API
     * (resource server)
     * accessToken is the token to call the Auth0 API
     * or a secured third-party API
     * extraParams.id_token has the JSON Web Token
     * profile has all the information from the user
     */
    return done(null, profile);
  }
);

/**
 *  App Configuration
 */

app.set('views', path.join(__dirname, 'views'));
app.set('view engine', 'jade');

app.use(logger('dev'));
app.use(express.json());
app.use(express.urlencoded({ extended: false }));
app.use(express.static(path.join(__dirname, 'public')));
app.use(expressSession(session));

passport.use(strategy);
app.use(passport.initialize());
app.use(passport.session());


passport.serializeUser((user, done) => {
  done(null, user);
});

passport.deserializeUser((user, done) => {
  done(null, user);
});

app.use((req, res, next) => {
  res.locals.isAuthenticated = req.isAuthenticated();
  next();
});

const secured = (req, res, next) => {
  if (req.user) {
    return next();
  }
  req.session.returnTo = req.originalUrl;
  res.redirect("/login");
};


/**
 * Routes Definitions
 */

app.get("/user", secured, (req, res, next) => {
  const { _raw, _json, ...userProfile } = req.user;
  res.render("user", {
    title: "Profile",
    userProfile: userProfile
  });
});

app.use('/', indexRouter);
app.use('/', authRouter);
app.use('/l', locationsRouter); // support short legacy urls. TODO: redirect.
app.use('/locations', locationsRouter);
app.use('/reporters', reportersRouter);
app.use('/buoys', buoysRouter);

// catch 404 and forward to error handler
app.use(function(req, res, next) {
  next(createError(404));
});

// error handler
app.use(function(err, req, res, next) {
  // set locals, only providing error in development
  res.locals.message = err.message;
  res.locals.error = req.app.get('env') === 'development' ? err : {};

  // render the error page
  res.status(err.status || 500);
  res.render('error');
});

/**
 * Note: Server Activation happens in bin/www
 */
module.exports = app;
