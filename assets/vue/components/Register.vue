<template>
    <div class="register-wrapper row justify-content-center mt-5">
        <div class="register-form col-6">
            <h1 class="form-title mb-3">Registration</h1>
            <div class="alert alert-success" role="alert" v-if="showSuccess">
                Registered successfully! Redirecting to the login page...
            </div>
            <div class="alert alert-danger" role="alert" v-if="submitErrorMessage">
                <small>{{ submitErrorMessage }}</small>
            </div>
            <div class="form-group">
                <label for="inputEmail">Email</label>
                <input
                    type="email"
                    name="inputEmail"
                    id="inputEmail"
                    class="form-control"
                    v-model="email"
                    v-on:change="validateEmail"
                    placeholder="Enter e-mail"
                    autofocus>
                <div class="form-input-error pt-2" v-if="formErrors.email">
                    <small class="error-text text-danger" v-for="errorMessage in formErrors.email">{{ errorMessage }}</small>
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword" v-model="password">Password</label>
                <input
                    type="password"
                    name="inputPassword"
                    id="inputPassword"
                    class="form-control"
                    v-model="password"
                    v-on:change="clearErrors('password')"
                    placeholder="Enter password">
                <div class="form-input-error pt-2" v-if="formErrors.password">
                    <small class="error-text text-danger" v-for="errorMessage in formErrors.password">{{ errorMessage }}</small>
                </div>
            </div>
            <div class="actions">
                <button class="btn btn-primary" type="submit" id="buttonSubmitRegister" v-on:click="submitForm">
                    <span class="loading" v-if="loading">
                        <span class="spinner-border spinner-border-sm"></span>
                        Loading...
                    </span>
                    <span class="waiting" v-else>Register</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    import $ from 'axios';
    import FormComponent from './base/FormComponent';

    export default {
        extends: FormComponent,
        name: "Register",
        props: {
            validateEmailRoute: {
                type: String,
                required: true
            },
            submitRedirectRoute: {
                type: String,
                required: true
            }
        },

        data() {
            return {
                email: null,
                password: null,
                formErrors: {
                    email: null,
                    password: null
                },
                loading: false,
                showSuccess: false
            }
        },

        methods: {
            submitForm() {
                this.loading = true;

                $.post(this.submitRoute, {
                    email: this.email,
                    password: this.password,
                    token: this.csrfToken
                })
                .then(this.handleSubmitFormSuccess)
                .catch(this.handleSubmitFormError)
            },

            validateEmail() {
                this.clearErrors('email');
                this.submitValidateEmail();
            },

            submitValidateEmail() {
                $.post(this.validateEmailRoute, {
                    email: this.email,
                })
                .then(this.handleSubmitValidateEmailSuccess);
            },

            handleSubmitFormSuccess(response) {
                const responseData = response.data;

                if (responseData && responseData.success) {
                    this.showSuccess = true;

                    setTimeout(() => {
                        window.location.replace(this.submitRedirectRoute);
                    }, 3500);
                } else if (responseData.errors) {
                    this.formErrors = {...this.formErrors, ...responseData.errors}
                }

                this.loading = false;
            },

            handleSubmitFormError() {
                this.loading = false;
                this.showSubmitErrorMessage();
            },

            handleSubmitValidateEmailSuccess(response) {
                const responseData = response.data;

                if (responseData && !responseData.success) {
                    this.formErrors = {...this.formErrors, ...responseData.errors}
                }
            },
        }
    }
</script>
