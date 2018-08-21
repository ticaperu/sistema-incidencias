<template>
    <div class="card">
        <div class="card-header">
            Crear Territorio Vecinal
        </div>
        <form action="" v-on:submit.prevent="newTerritorioVecinal()">
            <territorio-vecinal-form :territorioVecinal="territorioVecinal"  :errors="errors"></territorio-vecinal-form>

            <div class="card-footer">
                <button class="btn btn-primary" type="submit" :disabled="loading">
                    Guardar
                </button>
                <a class="btn btn-danger" :href="cancel_url">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                territorioVecinal: {
                    descripcion: ''
                },
                errors: [],
                loading: false,
                polygon:{},
                cancel_url:route('territorio-vecinal.index')
            }
        },
        methods: {
            newTerritorioVecinal() {
                this.loading = true;
                axios.post(route('territorio-vecinal.store'), this.territorioVecinal).then((response) => {
                    if (response.data.success) {
                        window.location.href = this.cancel_url;
                    }

                    this.loading = false;

                }).catch((error) => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    }

                    this.loading = false;

                })
            }
        }
    }
</script>