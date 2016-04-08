var providersList =
        {
            providers: [
                {
                    id: 'megafon',
                    name_ru: 'Мегафон',
                    img: 'images/providers/megafon.png',
                    host: '193.201.229.35',
                    domain: 'multifon.ru',
                    ref_link: 'http://multifon.ru/help'
                },
                {
                    id: 'MangoTel',
                    name_ru: 'Манго Телеком',
                    img: 'images/providers/MangoTel.png',
                    host: 'mangosip.ru',
                    ref_link: 'http://www.mango-office.ru/shop/tariffs/vpbx?p=400000034'
                },
                {
                    id: 'youmagicpro',
                    name_ru: 'Youmagic.pro',
                    img: 'images/providers/youmagicpro.png',
                    host: 'voip.mtt.ru',
                    ref_link: 'https://youmagic.pro/ru/services/mnogokanalnyj-nomer?aid=3643'
                }
            ]
        };
 
function getProvidersList() {
    for (var key in providersList.providers) {
        if (providersList.providers[key].ref_link){
            $('#provider_choose > .collection').append('<li class="collection-item">'+
            '<div class="left povider_logo_cont">'+
                '<img src="'+providersList.providers[key].img+'" alt="'+providersList.providers[key].name_ru+'" url="'+providersList.providers[key].host+'" ref="'+providersList.providers[key].ref_link+'" class="provider_logo">'+
            '</div>'+
            '<span class="title provider_name">'+providersList.providers[key].name_ru+'</span>'+
        '</li>');
        }else {
            $('#provider_choose > .collection').append('<li class="collection-item">'+
                '<div class="left povider_logo_cont">'+
                    '<img src="'+providersList.providers[key].img+'" alt="'+providersList.providers[key].name_ru+'" url="'+providersList.providers[key].host+'" class="provider_logo">'+
                '</div>'+
                '<span class="title provider_name">'+providersList.providers[key].name_ru+'</span>'+
            '</li>');
        }
       
    }
};

function getImgSipConnection(host) {
    if (host) {
        for (var key in providersList.providers) {
            if (providersList.providers[key].host == host) {
                return providersList.providers[key].img;
            }
        }
    }
    return;
}

function getNameProvConnection(host) {
    if (host) {
        for (var key in providersList.providers) {
            if (providersList.providers[key].host == host) {
                return providersList.providers[key].name_ru;
            }
        }
    }
    return;
}


function getHostSipConnection(name_ru) {
    if (name_ru) {
        for (var key in providersList.providers) {
            if (providersList.providers[key].name_ru == name_ru) {
                return providersList.providers[key].host;
            }
        }
    }
    return;
}

function getDomainSipConnection(name_ru) {
    if (name_ru) {
        for (var key in providersList.providers) {
            if (providersList.providers[key].name_ru == name_ru) {
                return providersList.providers[key].domain;
            }
        }
    }
    return;
}