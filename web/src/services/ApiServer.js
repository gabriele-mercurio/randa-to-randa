import axios from 'axios'

class ApiServer {
    static commonRequestConfig = {
        'Content-Type': 'multipart/form-data'
    };
    static base_url;
    static async get(endpoint, body = {}, config) {
        
        config = {...config, ...ApiServer.commonRequestConfig};
        try {
            let response = "";
            
            if(localStorage.getItem("TEST_MODE")) {
                let data = ApiServer.getData(endpoint);
                return Promise.resolve(data);
            } else {
                response = await axios.get(ApiServer.base_url + endpoint, body, config);
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
            let response = await axios.post(ApiServer.base_url + endpoint, body, {
                headers: {
                    'Constent-Type': 'multipart/form-data'
                }
            } );
            return ApiServer.parseResponse(response);
        } catch (e) {
            return null;
        }
    }

    static async login (endpoint, body, config) {
        config = {...config, ...ApiServer.commonRequestConfig};
        try {
            let response = await axios.post(ApiServer.base_url + endpoint, body, config );
            debugger;
            return ApiServer.parseResponse(response);
        } catch (e) {
            return null;
        }
    }

    static async put (endpoint, body, config) {
        config = {...config, ...ApiServer.commonRequestConfig};
        try {
            let response = await axios.put(ApiServer.base_url + endpoint, body, config );
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


    static getData(endpoint) {
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
                        warning: "CORE_GROUP",
                    }
                ]
        }
    }
}

export default ApiServer;