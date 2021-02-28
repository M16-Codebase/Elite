/**
 * Created by pahus on 12.07.2017.
 */



$(document).ready(function() {
    var personSearchEvent = 'PERSON_SEARCH',
        sellFlatEvent = 'SELL_FLAT',
        contactsEvent = 'CONTACTS',
        realEstateEvent = 'REAL_ESTATE',
        resaleEvent = 'RESALE',
        lookFlatEvent = 'LOOK_FLAT',
        curUrl = window.location.href,
        locationAsArray = _.compact(curUrl.split('/')),
        query = locationAsArray[2],
        subQuery = locationAsArray[3];
        
    $('form').find('button.btn.m-sand').click(function() {

        console.log('sdflskjlsdfkjl');
            var trackers = ga.getAll();
            trName = trackers[0].get('name');
            ga(trName + '.send', 'event', 'submit', 'test');
    });
    
    
    $('a.swiper-slide.btn.m-black').click(function() {
        if (query == 'real-estate' && subQuery == 'complex') {
            createFormSubmitEvent(realEstateEvent);
        }
    });

    $('a.btn.m.m-magenta-fill.m-vw').click(function() {
        if (query == 'resale') {
            createFormSubmitEvent(lookFlatEvent);
        }
        if (query == 'residential') {
            createFormSubmitEvent(lookFlatEvent);
        }
    });
});

function createFormSubmitEvent(eventName) {
    if ('yaCounter33436783' in window && yaCounter33436783.reachGoal) {
        yaCounter33436783.reachGoal(eventName);
    }
    if ('ga' in window) {
        var trackers = ga.getAll();
        trName = trackers[0].get('name');
        ga(trName + '.send', 'event', eventName, 'submit');
    }
                    
    if ('roistat' in window) {
        roistat.event.send(eventName);
    }
}
