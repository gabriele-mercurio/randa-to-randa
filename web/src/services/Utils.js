class Utils {
  static getMonthYear(date) {
    let d = date ? new Date(date) : new Date();
    let month = (d.getMonth() + 1).toString().padStart(2, "0");
    let year = d.getFullYear();
    return year + "-" + month;
  }

  static saveToStorage(key, value) {
    if (value) {
      localStorage.setItem(key, JSON.stringify(value));
    }
  }

  static getFromStorage(key) {
    let value = localStorage.getItem(key);
    return value ? JSON.parse(value) : false;
  }

  static removeFromStorage(key) {
    localStorage.removeItem(key);
  }

  static getCurrentTimeslot() {
    return "T" + this.getNumericTimeslot();
  }

  static getNumericTimeslot() {
    let month = new Date().getMonth() + 1;
    return Math.ceil(month / 3);
  }

  static getTimeslotFromMonth(month) {
    return Math.ceil(month / 3);
  }

  static getFirstTimeslotMonth(timeslot) {
    if (timeslot.toString().charAt(0) === "T") {
      timeslot = timeslot.substr(1, 1) * 1;
    }
    if (!timeslot) timeslot = 1;
    return (timeslot - 1) * 3 + 1;
  }
  
  static getNextTimeslot(timeslot) {
    return "T" + ((timeslot.substr(1, 1) * 1) + 1);
  }

  static getState(state) {
    switch(state) {
      case "TODO": return "Da compilare";
      case "APPR": return "Approvato";
      case "PROP": return "Proposto";
    }
  }

  static getChapterState(state) {
    switch(state) {
      case "CORE_GROUP": return "Core group";
      case "CHAPTER": return "Capitolo";
      case "PROJECT": return "Progetto";
      case "CLOSED": return "Chiuso";
    }
  }

  static getRandaState(state) {
    switch(state) {
      case "APPR": return "Approvato";
      case "TODO": return "Compilazione in corso";
      case "REFUSED": return "Rifiutato";
    }
  }
}

export default Utils;
