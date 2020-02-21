<script>
    export default {
        name: "FormComponent",
        props: {
            csrfToken: {
                type: String,
                required: true
            },
            submitRoute: {
                type: String,
                required: true
            }
        },

        data() {
            return {
                formErrors: null,
                submitErrorMessage: null,
                errorMessageDisplayTime: 6000
            }
        },

        methods: {
            clearErrors(field) {
                this.formErrors[field] = null;
            },

            showSubmitErrorMessage() {
                this.submitErrorMessage = 'There was an error while submitting data.';

                setTimeout(() => {
                    this.submitErrorMessage = null;
                }, this.errorMessageDisplayTime);
            }
        }
    }
</script>
