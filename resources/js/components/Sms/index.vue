<script>
import sms20Provider from "./Providers/sms20.vue";
import kavenegarProvider from "./Providers/kavenegar.vue";

export default {
    components: {
        sms20Provider,
        kavenegarProvider,
    },
    data: () => ({
        availableProviders: {},
        providers: {},
    }),
    methods: {
        fetchProviders() {
            return axios.get("/sms/providers").then(({ data }) => {
                this.providers = data.providers;
                this.availableProviders = data.availableProviders;
            });
        },
        findProviderSetting(providerName) {
            return this.providers.find((item) => item.provider == providerName);
        },
        checkConfig(
            providerName,
            username,
            password,
            apiKey,
            forUpdate = false,
        ) {
            if (!providerName) {
                this.$swal.fire({
                    title: "اخطار",
                    html: "ارائه دهنده مشخص نشده است",
                    icon: "error",
                    toast: true,
                    timerProgressBar: true,
                    timer: 3500,
                    position: "bottom-end",
                    rtl: true,
                });
                return false;
            }
            if (!forUpdate) {
                if ((!username || !password) && !apiKey) {
                    this.$swal.fire({
                        title: "اخطار",
                        html: "اطلاعات احراز هویت به درستی وارد نشده است",
                        icon: "error",
                        toast: true,
                        timerProgressBar: true,
                        timer: 3500,
                        position: "bottom-end",
                        confirmButtonText: "تایید!",
                    });
                    return false;
                }
            } else {
                let providerSetting = this.findProviderSetting(providerName);
                let isSetPassword = providerSetting && providerSetting.password;
                if (
                    ((!username && !isSetPassword) ||
                        (!username && !password)) &&
                    !apiKey
                ) {
                    this.$swal.fire({
                        title: "اخطار",
                        html: "اطلاعات احراز هویت به درستی وارد نشده است",
                        icon: "error",
                        toast: true,
                        timerProgressBar: true,
                        timer: 3500,
                        position: "bottom-end",
                        confirmButtonText: "تایید!",
                    });
                    return false;
                }
            }

            return true;
        },
        updateReceivedSms() {
            return axios.post("/sms/update/receiveSms").then(({ data }) => {
                if (data.sentSmsCount <= 0)
                    this.$swal.fire({
                        title: "موفقیت",
                        html: "اس ام اسی دریافت نشده است.",
                        icon: "success",
                        toast: true,
                        timerProgressBar: true,
                        timer: 3500,
                        position: "bottom-end",
                        confirmButtonText: "تایید!",
                    });
                else
                    this.$swal.fire({
                        title: "موفقیت",
                        html:
                            "تعداد <b> " +
                            data.sentSmsCount +
                            " </b> با موفقیت ارسال شد.",
                        icon: "success",
                        toast: true,
                        timerProgressBar: true,
                        timer: 3500,
                        position: "bottom-end",
                        confirmButtonText: "تایید!",
                    });
            });
        },
        /*================================ Functions used by children ================================*/
        saveConfig(
            providerName,
            username,
            password,
            apiKey,
            senderNumber = " ",
        ) {
            if (!this.checkConfig(providerName, username, password, apiKey))
                return;
            return axios
                .post("/sms/providers", {
                    provider: providerName,
                    username: username,
                    password: password,
                    apiKey: apiKey,
                    senderNumber: senderNumber,
                })
                .then(() => {
                    this.fetchProviders();
                    this.$swal.fire({
                        title: "موفقیت",
                        html: "تنظیمات با موفقیت ذخیره شد",
                        icon: "success",
                        toast: true,
                        timerProgressBar: true,
                        timer: 3500,
                        position: "bottom-end",
                        confirmButtonText: "تایید!",
                    });
                });
        },
        updateConfig(
            providerName,
            username,
            password,
            apiKey,
            senderNumber = " ",
        ) {
            if (
                !this.checkConfig(
                    providerName,
                    username,
                    password,
                    apiKey,
                    true,
                )
            )
                return;
            return axios
                .put("/sms/providers/" + providerName, {
                    username: username,
                    password: password,
                    apiKey: apiKey,
                    senderNumber: senderNumber,
                })
                .then(() => {
                    this.fetchProviders();
                    this.$swal.fire({
                        title: "موفقیت",
                        html: "تنظیمات با موفقیت ویرایش شد",
                        icon: "success",
                        toast: true,
                        timerProgressBar: true,
                        timer: 3500,
                        position: "bottom-end",
                        confirmButtonText: "تایید!",
                    });
                });
        },
        activate(providerName) {
            return axios
                .put("/sms/providers/" + providerName + "/activate")
                .then(() => {
                    this.fetchProviders();
                    this.$swal.fire({
                        title: "موفقیت",
                        html: "ارائه دهنده با موفقیت فعال شد.",
                        icon: "success",
                        toast: true,
                        timerProgressBar: true,
                        timer: 3500,
                        position: "bottom-end",
                        confirmButtonText: "تایید!",
                    });
                });
        },
        /*================================ /Functions used by children ================================*/
    },
    computed: {
        activeProvider: function () {
            return this.providers.find((item) => item.active);
        },
    },
    created() {
        this.fetchProviders();
    },
};
</script>

<template>
    <div class="container pt-5">
        <div class="row">
            <div class="col-12">
                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li
                        class="nav-item"
                        role="presentation"
                        v-for="(name, key, index) in availableProviders"
                        :key="'tab-btn-' + key"
                    >
                        <button
                            class="nav-link"
                            :class="{
                                active:
                                    (!activeProvider && index == 0) ||
                                    (activeProvider &&
                                        key == activeProvider.provider),
                            }"
                            :id="'pills-' + key + '-tab'"
                            data-bs-toggle="pill"
                            :data-bs-target="'#pills-' + key"
                            type="button"
                            role="tab"
                            :aria-controls="'pills-' + key"
                        >
                            {{ name }}
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="myTabContent">
                    <div
                        v-for="(name, key, index) in availableProviders"
                        class="tab-pane fade"
                        :class="{
                            'show active':
                                (!activeProvider && index == 0) ||
                                (activeProvider &&
                                    key == activeProvider.provider),
                        }"
                        :id="'pills-' + key"
                        :key="'tb-content-' + key"
                        role="tabpanel"
                        :aria-labelledby="key + '-tab'"
                    >
                        <component
                            :is="key + 'Provider'"
                            :settings="findProviderSetting(key)"
                        ></component>
                    </div>
                </div>
            </div>
            <div class="col-12 my-3">
                <hr />
            </div>
            <div class="col-8">
                <p class="fs-4">به‌روزرسانی پیام‌های دریافتی</p>
                <p>
                    اگر تنظیمات مربوط به
                    <b>Schedular</b>
                    فریمورک
                    <b>Laravel</b>
                    را انجام داده باشد. این عملیات به صورت خودکار
                    <b>هر 5 ثانیه</b>
                    انجام می‌شود.
                </p>
                <p>
                    اگر این کار را انجام نداده‌اید با استفاده از این دکمه
                    می‌توانید عملیات را انجام دهید.
                </p>
                <p>
                    با استفاده از این دکمه
                    <b>لیست پیام‌های دریافتی</b>
                    از طریق API به روزرسانی می‌شود. و
                    <b>پاسخ مناسب</b>
                    به آن‌ها ارسال می‌شود.
                </p>
            </div>
            <div class="col-4 d-flex justify-content-end align-items-center">
                <button
                    class="btn btn-lg btn-primary"
                    @click="updateReceivedSms"
                >
                    <i class="la la-refresh"></i>
                    به‌روزرسانی
                </button>
            </div>
        </div>
    </div>
</template>
