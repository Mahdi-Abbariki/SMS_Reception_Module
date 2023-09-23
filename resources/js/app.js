import "./bootstrap";

import Vue from "vue";

/*========================= Plugins =========================*/
//---->Sweet alert
import VueSweetalert2 from "vue-sweetalert2";
// If you don't need the styles, do not connect
import "sweetalert2/dist/sweetalert2.min.css";
Vue.use(VueSweetalert2);

//---->Axios
//axios settings
window.axios.interceptors.response.use(
    function (response) {
        return response;
    },
    function (error) {
        if (error.response !== undefined) {
            switch (error.response.status) {
                case 413: {
                    Vue.swal.fire({
                        title: "اخطار!",
                        html: "حجم فایل غیر مجاز است!",
                        icon: "error",
                        confirmButtonText: "تایید!",
                        toast: true,
                    });
                    break;
                }
                case 429: {
                    Vue.swal.fire({
                        title: "اخطار!",
                        html: "تعداد درخواست ها در حالت مجاز عبور کرده است، لطفا پس از چند دقیقه دوباره تلاش کنید!",
                        icon: "error",
                        confirmButtonText: "تایید!",
                        toast: true,
                    });
                    break;
                }
                case 422: {
                    let errors = error.response.data.errors;
                    let txt = "<ul class='list-unstyled'>";
                    for (const error in errors)
                        if (errors.hasOwnProperty(error)) {
                            let errorText = errors[error][0];
                            txt += "<li>" + errorText + "</li>";
                        }
                    txt += "</ul>";
                    Vue.swal.fire({
                        title: "اخطار",
                        html: txt,
                        icon: "error",
                        toast: true,
                        timerProgressBar: true,
                        timer: 3500,
                        position: "bottom-end",
                        confirmButtonText: "تایید!",
                    });
                    break;
                }
            }
        }
        return Promise.reject(error);
    },
);

/*========================= Components =========================*/
import Index from "./components/Sms/index.vue";

new Vue({
    render: (h) => h(Index),
}).$mount("#app");
