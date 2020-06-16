import axios from "axios";

let store;
let t;

class ApiServer {
  static commonRequestConfig = {
    headers: {
      "Content-Type": "application/json"
    }
  };

  static async login(email, password) {
    let loginData = {
      email: email,
      password: password,
      grant_type: "password",
      client_id: process.env.client_id,
      client_secret: process.env.client_secret
    };

    try {
      let data = await axios.post(process.env.base_url + "/token", loginData);
      ApiServer.setToken(data.data.access_token);
      let me = await ApiServer.get("me");
      return {
        token: data.data.access_token,
        user: me
      };
    } catch (e) {
      return false;
    }
  }

  static async logout() {
    try {
      await ApiServer.post("revoke");
      ApiServer.revokeToken();
      return true;
    } catch (e) {
      return false;
    }
  }

  static setToken(token) {
    ApiServer.commonRequestConfig["headers"]["Authorization"] =
      "Bearer " + token;
  }

  static revokeToken() {
    ApiServer.commonRequestConfig["headers"]["Authorization"] = null;
  }

  static async get(endpoint, config) {
    config = { ...config, ...ApiServer.commonRequestConfig };
    try {
      let response = await axios.get(
        process.env.base_url + "/" + endpoint,
        config
      );
      return ApiServer.parseResponse(response);
    } catch (e) {
      return ApiServer.parseError(e);
    }
  }

  static async post(endpoint, body, config) {
    config = { ...config, ...ApiServer.commonRequestConfig };
    try {
      let response = await axios.post(
        process.env.base_url + "/" + endpoint,
        body,
        config
      );
      return ApiServer.parseResponse(response);
    } catch (e) {
      return ApiServer.parseError(e);
    }
  }

  static async put(endpoint, body, config) {
    config = { ...config, ...ApiServer.commonRequestConfig };
    try {
      let response = await axios.put(
        process.env.base_url + "/" + endpoint,
        body,
        config
      );
      return ApiServer.parseResponse(response);
    } catch (e) {
      return ApiServer.parseError(e);
    }
  }

  static parseResponse(response) {
    if (!response || !response.status) {
      return null;
    }
    if (response.status.toString().startsWith("4")) {
      window.location = "login";
    }
    if (!response.data || !response.status.toString().startsWith("2")) {
      return null;
    }
    return response["data"];
  }

  static parseError(error) {
    let status = error.response ? error.response.status : null;
    let errorResponse = {
      error: true,
      message: "",
      errorCode: status
    };

    switch (status) {
      case 401:
        store.commit("setToken", null);
        store.commit("setRegion", null);
        errorResponse.message = t('authentication_error');
        window.location = "login";
        break;
      case 422:
        errorResponse.message = "Errore nell'assoziazione dei dati.";
        break;
      case 403:
        errorResponse.message = "Non autorizzato";
      default:
        //window.location = "login";
        break;
    }
    return errorResponse;
  }

  static getData(endpoint) {
    switch (endpoint) {
      case "chapters?region=1":
        return [
          {
            name: "Nome capitolo",
            current_status: "CORE_GROUP",
            director: {
              firstname: "Luigi",
              lastname: "Luigiotti"
            },
            core_group_launch: {
              prev: "12/03/2020",
              actual: null
            },
            chapter_launch: {
              prev: "12/09/2020",
              actual: null
            },
            members: 34
          },
          {
            name: "Nome capitolo 2",
            current_status: "CHAPTER",
            director: {
              firstname: "Luigi",
              lastname: "Luis"
            },
            core_group_launch: {
              prev: "12/05/2020",
              actual: null
            },
            chapter_launch: {
              prev: "12/09/2020",
              actual: null
            },
            members: 34,
            warning: "CORE_GROUP"
          }
        ];
    }
  }
}

if (process.browser) {
  window.onNuxtReady(({ $store, $t }) => {
    console.log($t);
    store = $store;
  });
}

export default ApiServer;
