module.exports = ({ log, exec }) => {
  return Promise.resolve()
    .then(() => {
      log('Updating composer...');
      return exec('composer update');
    })
    .then(() => {
      log('Installing composer...');
      return exec('composer install');
    })
};
