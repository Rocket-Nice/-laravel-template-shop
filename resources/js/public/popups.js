import {setCookie, getCookie} from "./utilites";
import {Fancybox} from "@fancyapps/ui";

// gold ticket
// function goldTicketCheck() {
//   const goldTicket = getCookie('goldticket');
//   const goldTicketShown = getCookie('goldticketShown');
//
//   if (window.location.pathname == '/order' || window.location.pathname == '/catalog/zolotoy_bilet') {
//
//     console.log('window.location.pathname', window.location.pathname)
//     return;
//   }
//   if (goldTicketShown) {
//     return; // Если куки goldticketShown существует, функцию больше не запускаем
//   }
//
//   if (!goldTicket) {
//     // Если куки goldticket не существует, создаем его с текущей датой и временем
//     const now = new Date();
//     setCookie('goldticket', now.toUTCString(), 1);
//   } else {
//     // Если куки goldticket существует, проверяем дату и время создания
//     const createdTime = new Date(goldTicket);
//     const currentTime = new Date();
//     const diffMinutes = (currentTime - createdTime) / 1000;
//
//     if (diffMinutes >= 20) {
//       // Если прошло больше 20 минут, функция должна сработать без таймера
//       showGoldTicket();
//     } else {
//       // Если прошло меньше 20 минут, запускаем таймер
//       const remainingTime = (20 - diffMinutes) * 1000;
//       setTimeout(showGoldTicket, remainingTime);
//     }
//   }
// }
function goldTicketCheck() {
  const goldTicket = getCookie('goldticket');
  const goldTicketShown = getCookie('goldticketShown');

  if (window.location.pathname == '/order' || window.location.pathname == '/catalog/zolotoy_bilet') {
    console.log('window.location.pathname', window.location.pathname)
    return;
  }
  if (goldTicketShown) {
    return; // Если куки goldticketShown существует, функцию больше не запускаем
  }

  setTimeout(()=>{
    Fancybox.show(
      [
        {
          src: '#goldticket-alert',
          width: "900px",
          height: "700px",
        },
      ],
      {
        closeButton: false,
        Toolbar: {
          display: {
            left: [],
            middle: [],
            right: [],
          },
        },
        loop: false,
        touch: false,
        contentClick: false,
        dragToClose: false,
      }
    );
    setCookie('goldticketShown', 'true', false, 20);
  },5000)
}

function checklistPopup(){
  if(window.checklist && window.checklist.checklist){
    if(document.getElementById('hidden-alert')){
      document.getElementById('hidden-alert').addEventListener('click', function (e) {
        e.preventDefault()
        setCookie('checklist_pdf', 'true', 365);
        Fancybox.close()
      });
    }

    setTimeout(()=>{
      if (!getCookie('checklist_pdf')) {
        Fancybox.show(
          [
            {
              src: '#pdf-alert',
              width: "900px",
              height: "700px",
            },
          ],
          {
            closeButton: false,
            Toolbar: {
              display: {
                left: [],
                middle: [],
                right: [],
              },
            },
            loop: false,
            touch: false,
            contentClick: false,
            dragToClose: false,
          }
        );
      }
    },5000)
  }
}
// Проверяем наличие куки goldticket при загрузке страницы
if(window.goldTicket){
  document.addEventListener('DOMContentLoaded', goldTicketCheck);
}else{
  document.addEventListener('DOMContentLoaded', checklistPopup);

}
document.addEventListener('DOMContentLoaded', () => {

  if(window.checklist && window.checklist.checklist) {
    window.checklist.checklist_link.href = window.checklist.checklist;
  }
});
