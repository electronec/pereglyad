        var media = {
            dataProvider: {
                title: "Перегляд зібрання",
                source: [{
                    url: "http://194.165.46.178:18180/hls/transl.m3u8",
                    contentType: "movie",
                    width: 640,
                    height: 360
                }],
                splashImages: [{
                    url: "img/poster1.jpg",
                    width: 640,
                    height: 360
                }]
            }
        };
        var element = document.getElementById("player");
        window.bigsoda.player.create(element, media);

