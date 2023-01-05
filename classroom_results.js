$(".room").on("click", function(){
    
    $(this).next().slideToggle();
    if($(this).next().hasClass("expanded")){
        $(this).next().removeClass("expanded");
    }
    else{
        $(this).next().addClass("expanded");
        // let building = $(this).children("building");
        // document.querySelector(".building").innerHTML;
        // console.log(building);
        // ajaxGet("backend.php?building=" + building + "room=" + room, function(results){
        // });
    }
    
    
});

$(".collapse-btn").on("click", function(){
    $(".supplemental").each(function(index, element){
        if($(this).hasClass("expanded")){
            $(this).slideToggle();
            $(this).removeClass("expanded")
        }
    });
});

function to12HourFormat(date = (new Date)) {
    return {
        hours: ((date.getHours() + 11) % 12 + 1),
        minutes: (date.getMinutes() < 10 ? '0' : '') + date.getMinutes(),
        meridian: (date.getHours() >= 12) ? 'PM' : 'AM',
    };
}


function ajaxGet(endpointUrl, returnFunction) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', endpointUrl, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            if (xhr.status == 200) {
                returnFunction(xhr.responseText);
            } else {
                alert('AJAX Error.');
                console.log(xhr.status);
            }
        }
    }
    xhr.send();
};

