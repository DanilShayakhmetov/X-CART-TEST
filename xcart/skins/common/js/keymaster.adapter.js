(function(){
    window.keymaster = key.noConflict();

    define('keymaster/key', function() { return window.keymaster; });
})();