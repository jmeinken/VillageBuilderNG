
app.service("Request", function($http, State) {
    
    self = this;
    
    
    this.loadForm = function(name, url, parameters) {
        parameters = parameters || {};
        $http.get(url, {params: parameters}).
            success(function(data, status, headers, config){
                self[name] = {};
                self[name].request = data.values;
                self[name].originalRequest = JSON.parse(JSON.stringify(data.values));
                self[name].meta = data.meta;
                self[name].inputErrors = {};
                self[name].formError = '';
                //State.debug = data;
            }).
            error(function(data, status, headers, config) {
                State.debug = data;
                State.authenticate();
            });
    }
    
    this.loadArrayForm = function(name, url, parameters, arrayValue) {
        parameters = parameters || {};
        $http.get(url, {params: parameters}).
            success(function(data, status, headers, config){
                if (typeof(self[name])==='undefined') self[name] = [];
                self[name][arrayValue] = {};
                self[name][arrayValue].request = data.values;
                self[name][arrayValue].originalRequest = JSON.parse(JSON.stringify(data.values));
                self[name][arrayValue].meta = data.meta;
                self[name][arrayValue].inputErrors = {};
                self[name][arrayValue].formError = '';
                //State.debug = data;
            }).
            error(function(data, status, headers, config) {
                State.debug = data;
                State.authenticate();
            });
    }
    

    
    
    
    
    
    //this could be used if you want to run the ajax get request in the 
    //controller instead of using the Request service to run it
    this.createRequest = function(name, data) {
        this[name] = {};
        this[name].name = name;
        this[name].request = data.values;
        this[name].originalRequest = JSON.parse(JSON.stringify(data.values));
        this[name].meta = data.meta;
        this[name].inputErrors = {};
        this[name].formError = '';
    }
    
    this.reset = function(name) {
        this[name].request = JSON.parse(JSON.stringify(this[name].originalRequest));
    }
    

 

});