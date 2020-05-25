import axios from 'axios'

const URL = "http://localhost/";

class ApiServer {
    static commonRequestConfig = {
        "Content-type": "application/json"
    };
    static async get(endpoint, body = {}, config) {
        
        config = {...config, ...ApiServer.commonRequestConfig};
        try {
            let response = "";
            
            if(localStorage.getItem("TEST_MODE")) {
                return Promise.resolve(JSON.parse(getData(endpoint)));
            } else {
                response = await axios.get(URL + endpoint, body, config);
                if(response.status == 200) {
                    return ApiServer.parseResponse(response);
                } else if(response.status.toString().startsWith("4")) {
                    window.location="login";
                }
            }
            
        } catch (e) {
            return null;
        }
    }

    static async post (endpoint, body, config) {
        config = {...config, ...ApiServer.commonRequestConfig};
        try {
            let response = await axios.post(URL + endpoint, body, config );
            return ApiServer.parseResponse(response);
        } catch (e) {
            return null;
        }
    }

    static async put (endpoint, body, config) {
        config = {...config, ...ApiServer.commonRequestConfig};
        try {
            let response = await axios.put(URL + endpoint, body, config );
            return ApiServer.parseResponse(response);
        } catch (e) {
            return null;
        }
    }

    static parseResponse(response) {
        if(!response || !response.status) {
            return null;
        }
        if(response.status.toString().startsWith("4")) {
            window.location = "login";
        }
        if(!response.data || response.status !== 200) {
            return null;
        }
        return;
        //return response.data.data ?? (response.data.content ?? response.data);
    }



    getData(endpoint) {
        switch(endpoint) {
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
                    }
                ]
            break;
        }
    }
}

export default ApiServer;
