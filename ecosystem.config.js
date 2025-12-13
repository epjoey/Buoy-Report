module.exports = {
  apps: [
    {
      name: 'buoyreport',
      script: 'app.js',
      cwd: '/home/ubuntu/buoyreport',
      env: {
        NODE_ENV: 'production'
      }
    }
  ]
};