<script>
export default {
    props: {
        settings: {},
    },
    data: () => ({
        providerName: "kavenegar",
        apiKey: null,
        senderNumber: null,
    }),
    methods: {
        saveOrUpdateConfig() {
            if (!this.settings) this.saveConfig();
            else this.updateConfig();
        },
        saveConfig() {
            this.$parent.saveConfig(
                this.providerName,
                null,
                null,
                this.apiKey,
                this.senderNumber,
            );
        },
        updateConfig() {
            this.$parent.updateConfig(
                this.providerName,
                null,
                null,
                this.apiKey,
                this.senderNumber,
            );
        },
    },
    created() {
        if (this.settings) {
            this.apiKey = this.settings.api_key;
            this.senderNumber = this.settings.sender_number;
        }
    },
};
</script>

<template>
    <div class="row">
        <div class="d-flex justify-content-between">
            <p class="fs-4 my-2">
                تنظمیات پنل پیامکی کاوه‌نگار
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
        <div class="col-4 mb-3">
            <label for="username" class="form-label">نام کاربری</label>
            <input
                type="text"
                class="form-control"
                id="username"
                v-model="apiKey"
            />
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
