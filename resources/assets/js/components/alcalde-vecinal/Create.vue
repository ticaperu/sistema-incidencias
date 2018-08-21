<template>
    <div class="card">
        <div class="card-header">
            Crear Alcalde Vecinal
        </div>
        <form action="" v-on:submit.prevent="newNivelAgua()">
            <div class="card-body">
                <alcalde-vecinal-form :alcaldeVecinal="alcaldeVecinal" :errors="errors"></alcalde-vecinal-form>
            </div>
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
       data(){
           return {
               alcaldeVecinal: {
                   territorio_vecinal_id: ''
               },
               errors: [],
               loading:false,
               cancel_url: route('alcalde-vecinal.index')
           }
       },
        methods: {
            newNivelAgua(){
                this.loading = true;
                axios.post(route('alcalde-vecinal.store'), this.alcaldeVecinal).then( (response) =>{
                    if(response.data.success)
                    {
                        window.location.href = this.cancel_url;
                    }

                    this.loading = false;

                }).catch((error) =>{
                    if(error.response.status == 422)
                    {
                        this.errors = error.response.data.errors;
                    }

                    this.loading = false;

                })
            }
        }
    }
</script>