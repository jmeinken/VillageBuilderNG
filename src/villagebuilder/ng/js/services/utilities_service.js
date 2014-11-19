app.service("Utilities", function() {
    
    this.keyArray = function(obj) {  
        var arr = [];
        var i=0;
        for (var key in obj) {
            arr[i] = key;
            i++;
        }
        return arr;
    }
    
});


