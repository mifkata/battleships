var GameHelper = {
    getGameIdFromQuery: function() {
        var query = document.location.search.substr(1);
        var decoded = query.replace(/&amp;/g, '&')
        var parse = decoded.split('&');
        var split;
        for (i = 0; i < parse.length; i++) {
            split = parse[i].split('=');
            if ('gameId' == split[0])
                return split[1];
        }
    }
}