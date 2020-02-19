<template>
    <div class="register-wrapper">
        <div class="register-form">
            <h1 class="form-title">Registration</h1>
            <div class="form-error">
                <span class="error-text">{{ formError }}</span>
            </div>
            <div class="form-group">
                <label for="inputEmail">Email</label>
                <input type="text" name="inputEmail" id="inputEmail" autofocus v-model="email">
                <div class="form-input-error">
                    <span class="error-text">{{ emailError }}</span>
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword" v-model="password">Password</label>
                <input type="password" name="inputPassword" id="inputPassword" v-model="password">
            </div>
            <div class="actions">
                <button class="btn btn-primary" type="submit" id="buttonSubmitRegister" v-on:click="submitForm">Register</button>
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
            formPrefix: {
                type: String,
                required: true
            }
        },

        data() {
            return {
                email: null,
                password: null,
                emailError: null,
                formError: null
            }
        },

        methods: {
            submitForm() {
                const emailInput = this.formatInputName('email');
                const passwordInput = this.formatInputName('plainPassword');

                $.post(this.submitRoute, {
                    emailInput: this.email,
                    passwordInput: this.password
                })
                .then((response) => {
                    console.log(response);
                })
                .fail((response) => {
                    console.log(response);
                });
            },

            formatInputName(name) {
                return this.formPrefix + '[' + name + ']';
            }
        }
    }
</script>
