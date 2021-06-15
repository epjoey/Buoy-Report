const env = process.env;

const config = {
  db: { /* don't expose password or any sensitive info, done only for demo */ 
    host: env.DB_HOST || 'localhost',
    user: env.DB_USER || 'root',
    password: env.DB_PASSWORD || 'mysqlpw99',
    database: env.DB_NAME || 'br',
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0
  },
  listPerPage: env.LIST_PER_PAGE || 1000,
};


module.exports = config;