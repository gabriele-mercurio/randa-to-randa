module.exports = ({ log, exec }) => {
  return Promise.resolve()
    .then(() => {
      log('Applying migrations...');
      return exec('./bin/console doctrine:migration:migrate');
    })
    .then(() => {
      log('Clearing cache...');
      return exec('./bin/console cache:clear');
    })
    .then(() => {
      log('Changing permissions for writable folders...');
      return exec('chown -R www-data:www-data public var');
    });
};
