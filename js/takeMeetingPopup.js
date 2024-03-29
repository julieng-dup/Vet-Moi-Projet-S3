//////////////////////
// Popup Background //
//////////////////////
let meetingAnimalId = '';
let meetingSpeciesId = '';
let meetingChooseDate = '';
let meetingTimeSlotId = '';
let meetingTimeSlotTypeId = '';
let meetingVetoId = '';
let meetingYear = 2020;
let meetingWeek = 1;


let popupBackground = document.createElement("div");
popupBackground.id = "popupBackground";
let st = popupBackground.style;
st.width = '100%';
st.height = '100%';
st.backgroundColor = 'rgba(0,0,0, 0.4)';
st.backdropFilter = 'blur(5px)';
st.position = 'fixed';
st.display = 'flex';
st.justifyContent = 'center';
st.alignItems = 'center';
popupBackground.hidden = true;

///////////
// Popup //
///////////
let popup = document.createElement("div");
popup.id = "popup";
let edSt = popup.style;
edSt.padding = '20px 50px';
edSt.borderRadius = '20px';
edSt.backgroundColor = "#DDDDDD";
edSt.display = 'flex';
edSt.flexDirection = 'column';
edSt.alignItems = 'center';
edSt.gap = '20px';

///////////
// Title //
///////////
let title = document.createElement("span");
title.innerText = "Rendez-Vous";
title.style.fontSize = "28px";

////////////////
// Bottom Div //
////////////////
var actionListDiv = document.createElement("div");
actionListDiv.id = "actionListDiv";
actionListDiv.style.display = "flex";
actionListDiv.style.columnGap = "10px";

///////////////////
// Cancel Button //
///////////////////
var cancelButton = document.createElement("input");
cancelButton.id = "cancelButton"
cancelButton.type = "submit";
cancelButton.className = "button";
cancelButton.value = "Annuler";
cancelButton.style.padding = "12px 25px";
cancelButton.style.fontSize = "18px";
cancelButton.style.backgroundColor = "#C20D0D";
cancelButton.style.transition = "0.2s background-color ease-in-out";
cancelButton.onmouseover = function() {
    this.style.backgroundColor = "#810000";
}

cancelButton.onmouseleave = function(){
    this.style.backgroundColor = "#C20D0D";
}
cancelButton.onclick = function(){
    onExitPopup();
    this.style.backgroundColor = "#c20d0d";
}

///////////////
// Day Title //
///////////////

var dayTitle = document.createElement("span");
dayTitle.id = "dayTitle";
dayTitle.style.fontSize = "18px";


/////////////////////
// Continue Button //
/////////////////////
var continueButton = document.createElement("input");
continueButton.id = "continueButton"
continueButton.type = "submit";
continueButton.className = "button";
continueButton.value = "Continuer";
continueButton.style.padding = "12px 25px";
continueButton.style.fontSize = "18px";
continueButton.onclick = function() {
    onExitPopup();
}

/////////////////////////
// Take Meeting Button //
/////////////////////////
let takeMeetingButton = document.createElement("input");
takeMeetingButton.id = "deleteMeetingButton";
takeMeetingButton.type = "submit";
takeMeetingButton.className = "button";
takeMeetingButton.value = "Prendre rendez-vous";
takeMeetingButton.style.padding = "12px 25px";
takeMeetingButton.style.fontSize = "18px";
takeMeetingButton.onclick = function() {

    console.log(meetingVetoId);
    console.log(meetingAnimalId);
    console.log(meetingTimeSlotId);
    console.log(meetingSpeciesId);
    console.log(meetingChooseDate);

    let ajaxRequest = new AjaxRequest(
        {
            url: "trmt/take_meeting_trmt.php",
            method: 'get',
            handleAs: 'json',
            parameters: {
                vetoId: meetingVetoId,
                animalId: meetingAnimalId,
                timeSlotId: meetingTimeSlotId,
                speciesId: meetingSpeciesId,
                chooseDate: meetingChooseDate
            },
            onSuccess: function (res) {
                console.log(res);

                if(res['success']){
                    clearPopupContainer();
                    popup.appendChild(title);
                    let success = document.createElement('span');
                    success.style.fontSize = '24px';
                    success.innerText = "Rendez-vous pris avec succès !";
                    popup.appendChild(success);
                    popup.appendChild(continueButton);

                    document.getElementById('timeSlot-' + meetingTimeSlotId).remove();
                }else{

                }

            },
            onError: function (status, message) {
            }
        });
}

///////////////
// Functions //
///////////////
appendOriginalElement();
document.onclick = function(e){
    if(e.target.id == 'popupBackground') {
        onExitPopup();
    }
}

function appendOriginalElement() {
    popupBackground.appendChild(popup);
    document.body.appendChild(popupBackground);
}

function onExitPopup() {
    hideEditMeetingPopup();
    clearPopupContainer();
    appendOriginalElement();
    enableScroll();
}


function onOpenPopup(aId, sId, vId, tsId, tstId, y, w) {

    meetingAnimalId = aId;
    meetingSpeciesId = sId;
    meetingVetoId = vId;
    meetingTimeSlotId = tsId;
    meetingTimeSlotTypeId = tstId;
    meetingYear = y;
    meetingWeek = w;


    setMeetingChooseDate(dayTitle);

    title.innerText = "Rendez-Vous";
    document.getElementById("popupBackground").hidden = false;
    disableScroll();
    updatePopup();
}

function setMeetingChooseDate(dayTitle){
    meetingChooseDate = getDateOfISOWeek(meetingWeek, meetingYear);
    new AjaxRequest({
        url: "api/getTimeSlotInformation.php",
        method: 'get',
        handleAs: 'json',
        parameters: {
            timeSlotId: meetingTimeSlotId,
        },
        onSuccess: function (res) {

            switch(res[0]['dayName'])
            {
                case "Mardi":
                    meetingChooseDate.setDate(meetingChooseDate.getDate() + 1);
                    break;
                case "Mercredi":
                    meetingChooseDate.setDate(meetingChooseDate.getDate() + 2);
                    break;
                case "Jeudi":
                    meetingChooseDate.setDate(meetingChooseDate.getDate() + 3);
                    break;
                case "Vendredi":
                    meetingChooseDate.setDate(meetingChooseDate.getDate() + 4);
                    break;
                case "Samedi":
                    meetingChooseDate.setDate(meetingChooseDate.getDate() + 5);
                    break;
                case "Dimanche":
                    meetingChooseDate.setDate(meetingChooseDate.getDate() + 6);
                    break;
            }

            let day = meetingChooseDate.getDate();
            let month = meetingChooseDate.getMonth()+1;
            let year = meetingChooseDate.getFullYear();
            meetingChooseDate = day +'/'+month+'/'+year;
            dayTitle.innerText = "Date: " + meetingChooseDate;
        },
        onError: function (status, message) {
        }
    });
}

function clearPopupContainer() {
    let popup = document.getElementById("popup");
    popup.querySelectorAll('*').forEach(n => n.remove());
}

function hideEditMeetingPopup() {
    document.getElementById("popupBackground").hidden = true;
}

function updatePopup()
{

    let timeSlotTitle = document.createElement("span");
    timeSlotTitle.id = "timeSlotTitle";
    timeSlotTitle.innerText = "Horaire: " + document.getElementById("timeSlot-"+ meetingTimeSlotId).innerText;
    timeSlotTitle.style.fontSize = "18px";

    actionListDiv.appendChild(cancelButton);
    actionListDiv.appendChild(takeMeetingButton);

    popup.appendChild(title);
    popup.appendChild(dayTitle);
    popup.appendChild(timeSlotTitle);
    popup.appendChild(actionListDiv);

}

/*
 *
 *
 * Merci StackOverFlow pour le lock scroll
 *
 */

var keys = {37: 1, 38: 1, 39: 1, 40: 1};

function preventDefault(e) {
    e.preventDefault();
}

function preventDefaultForScrollKeys(e) {
    if (keys[e.keyCode]) {
        preventDefault(e);
        return false;
    }
}

// modern Chrome requires { passive: false } when adding event
var supportsPassive = false;
try {
    window.addEventListener("test", null, Object.defineProperty({}, 'passive', {
        get: function () { supportsPassive = true; }
    }));
} catch(e) {}

var wheelOpt = supportsPassive ? { passive: false } : false;
var wheelEvent = 'onwheel' in document.createElement('div') ? 'wheel' : 'mousewheel';

// call this to Disable
function disableScroll() {
    window.addEventListener('DOMMouseScroll', preventDefault, false); // older FF
    window.addEventListener(wheelEvent, preventDefault, wheelOpt); // modern desktop
    window.addEventListener('touchmove', preventDefault, wheelOpt); // mobile
    window.addEventListener('keydown', preventDefaultForScrollKeys, false);
}

// call this to Enable
function enableScroll() {
    window.removeEventListener('DOMMouseScroll', preventDefault, false);
    window.removeEventListener(wheelEvent, preventDefault, wheelOpt);
    window.removeEventListener('touchmove', preventDefault, wheelOpt);
    window.removeEventListener('keydown', preventDefaultForScrollKeys, false);
}


function getDateOfISOWeek(w, y) {
    let simple = new Date(y, 0, 1 + (w - 1) * 7);
    let dow = simple.getDay();
    let ISOweekStart = simple;
    if (dow <= 4)
        ISOweekStart.setDate(simple.getDate() - simple.getDay() + 1);
    else
        ISOweekStart.setDate(simple.getDate() + 8 - simple.getDay());
    return ISOweekStart;
}