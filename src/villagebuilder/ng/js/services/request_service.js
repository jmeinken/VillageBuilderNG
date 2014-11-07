
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
                //State.debug = status;
            }).
            error(function(data, status, headers, config) {
                
            });
    }
    
    
    
    
    
    //this could be used if you want to run the ajax get request in the 
    //controller instead of using the Request service to run it
    this.createRequest = function(name, data) {
        this[name] = {};
        this[name].request = data.values;
        this[name].originalRequest = JSON.parse(JSON.stringify(data.values));
        this[name].meta = data.meta;
        this[name].inputErrors = {};
        this[name].formError = '';
    }
 

});