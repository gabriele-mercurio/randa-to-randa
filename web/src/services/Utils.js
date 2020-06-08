class Utils {
  static getMonthYear(date) {
    let d = new Date(date);
    let month = (d.getMonth() + 1 ).toString().padStart(2, '0');
    let year = d.getFullYear();
    return year + "-" + month;
  };
  
  static saveToStorage(key, value) {
    if(value) {
      localStorage.setItem(key, JSON.stringify(value));
    }
  };

  static getFromStorage(key) {
    let value = localStorage.getItem(key);
    return value ? JSON.parse(value) : false;
  };

  static removeFromStorage(key) {
    localStorage.removeItem(key);
  };
}

export default Utils;
