class Utils {
  static getMonthYear(date) {
    let d = new Date(date);
    let month = d.getMonth().toString().padStart(2, '0');
    let year = d.getFullYear();
    return year + "-" + month;
  }
}

export default Utils;
