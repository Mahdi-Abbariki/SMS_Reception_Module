<script>
//982100066381362
export default {
    props: {
        settings: {},
    },
    data: () => ({
        providerName: "sms20",
        username: null,
        password: null,
        senderNumber: null,
    }),
    methods: {
        saveOrUpdateConfig() {
            if (!this.settings) this.saveConfig();
            else this.updateConfig();
        },
        saveConfig() {
            this.$parent
                .saveConfig(
                    this.providerName,
                    this.username,
                    this.password,
                    null,
                    this.senderNumber,
                )
                .then(() => {
                    this.password = null;
                });
        },
        updateConfig() {
            this.$parent
                .updateConfig(
                    this.providerName,
                    this.username,
                    this.password,
                    null,
                    this.senderNumber,
                )
                .then(() => {
                    this.password = null;
                });
        },
    },
    computed: {
        isSetPassword: function () {
            return !!(this.settings && this.settings.password);
        },
    },
    created() {
        if (this.settings) {
            this.username = this.settings.username;
            this.senderNumber = this.settings.sender_number;
        }
    },
};
</script>

<template>
    <div class="row">
        <div class="d-flex justify-content-between">
            <p class="fs-4 my-2">
                تنظمیات پنل پیامکی SMS20
                <span
                    class="badge fs-6 rounded-pill bg-success"
                    v-if="settings && settings.active"
                >
                    فعال
                </span>
            </p>
            <div>
                <button
                    v-if="!settings || !settings.active"
                    class="btn btn-sm btn-warning"
                    @click="$parent.activate(providerName)"
                >
                    <i class="la la-check"></i>
                    فعال سازی
                </button>
            </div>
        </div>
        <div class="col-12">
            <div class="alert alert-danger" role="alert">
                با بررسی چندین روش مختلف، این وبسرویس پاسخ درستی نمی‌دهد. از
                ارائه دهنده های دیگر استفاده کنید.
            </div>
        </div>
        <div class="col-4 mb-3">
            <label for="username" class="form-label">نام کاربری</label>
            <input
                type="text"
                class="form-control"
                id="username"
                v-model="username"
            />
        </div>
        <div class="col-4 mb-3">
            <label for="password" class="form-label">رمز عبور</label>
            <input
                type="password"
                class="form-control"
                id="password"
                v-model="password"
                autocomplete="new-password"
                aria-describedby="passwordHelp"
            />
            <div id="passwordHelp" class="form-text" v-if="isSetPassword">
                اگر می‌خواهید رمزعبور را تغییر دهید، این فیلد را وارد کنید.
            </div>
        </div>
        <div class="col-4 mb-3">
            <label for="senderNumber" class="form-label"
                >شماره ارسال کننده</label
            >
            <input
                type="text"
                class="form-control"
                id="senderNumber"
                v-model="senderNumber"
            />
        </div>
        <div class="col-12 d-flex justify-content-end">
            <button class="btn btn-success" @click="saveOrUpdateConfig">
                <i class="la la-plus"></i>
                ذخیره
            </button>
        </div>
    </div>
</template>
