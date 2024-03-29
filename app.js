#!/usr/bin/env node

/**
 * Required External Modules
 */
const createError = require('http-errors');
const express = require('express');
const path = require('path');
const passport = require("passport");
const Auth0Strategy = require("passport-auth0");
const logger = require('morgan');
const cookieSession = require('cookie-session')
const cookieParser = require('cookie-parser');
const debug = require('debug')('buoy-report:server');
const http = require('http');

require("dotenv").config(); // loads the vars from the .env file into process.env
const ADMIN_EMAIL = "jhodara@gmail.com";

/**
 * Required Internal Modules
 */
const db = require('./db');
const authRouter = require("./routes/auth");
const viewsRouter = require('./routes/views');
const apiRouter = require('./routes/api');

/**
 * App Variables
 */
const app = express();

/**
 * Session Configuration (New!)
 */
const session = {
  secret: process.env.SESSION_SECRET,
  cookie: {},
  resave: false,
  saveUninitialized: false
};

// if(process.env.NODE_ENV === "production"){
//   // Serve secure cookies, requires HTTPS
//   session.cookie.secure = true;
// }

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
// https://stackoverflow.com/questions/39930070/nodejs-express-why-should-i-use-app-enabletrust-proxy
app.enable('trust proxy');

app.set('views', path.join(__dirname, 'views'));
app.set('view engine', 'jade');

app.use(logger('dev'));
app.use(express.json());
app.use(express.urlencoded({ extended: false }));
app.use(express.static(path.join(__dirname, 'dist')));
app.use(express.static(path.join(__dirname, 'static')));
app.use(cookieParser());
app.use(cookieSession({
  name: 'session',
  keys: ['key1', 'key2'],

  // Cookie Options
  maxAge: 14 * 24 * 3600000 //2 week
}))

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
  if(req.user){
    req.user.isAdmin = req.user._json.email === ADMIN_EMAIL;
  }
  res.locals.isAuthenticated = req.isAuthenticated();
  res.locals.user = req.user ? req.user._json : {};
  res.locals.user.isAdmin = req.user && req.user._json.email === ADMIN_EMAIL;
  res.locals.NODE_ENV = process.env.NODE_ENV;
  next();
});


/**
 * Routes Definitions
 */

app.use('/api', apiRouter);
app.use('/', authRouter);
app.use('/', viewsRouter);

// catch 404 and forward to error handler
app.use(function(req, res, next) {
  next(createError(404));
});

// error handler
app.use(function(err, req, res, next) {
  // set locals, only providing error in development
  res.locals.message = err.message;
  res.locals.error = req.app.get('env') === 'development' ? err : {};
  // render th error page
  res.status(err.status || 500);
  res.render('error');
});


/**
 * Get port from environment and store in Express.
 */

var port = process.env.PORT || '3000';
app.set('port', port);


/**
 * Event listener for HTTP server "error" event.
 */

function onError(error) {
  if (error.syscall !== 'listen') {
    throw error;
  }

  var bind = typeof port === 'string'
    ? 'Pipe ' + port
    : 'Port ' + port;

  // handle specific listen errors with friendly messages
  switch (error.code) {
    case 'EACCES':
      console.error(bind + ' requires elevated privileges');
      process.exit(1);
      break;
    case 'EADDRINUSE':
      console.error(bind + ' is already in use');
      process.exit(1);
      break;
    default:
      throw error;
  }
}

/**
 * Event listener for HTTP server "listening" event.
 */

function onListening() {
  var addr = server.address();
  var bind = typeof addr === 'string'
    ? 'pipe ' + addr
    : 'port ' + addr.port;
  console.log('Listening on ' + bind);
  debug('Listening on ' + bind);
}


/**
 * Create HTTP server.
 */

var server = http.createServer(app);

/**
 * Listen on provided port, on all network interfaces.
 */

server.listen(port);
server.on('error', onError);
server.on('listening', onListening);
