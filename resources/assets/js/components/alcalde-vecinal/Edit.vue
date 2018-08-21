<template>
    <div class="card">
        <div class="card-header">
            Editar Alcalde Vecinal
        </div>
        <form action="" v-on:submit.prevent="editNivelAgua()">
            <div class="card-body">
                <alcalde-vecinal-form v-if="alcaldeVecinal.id" :alcaldeVecinal="alcaldeVecinal" :errors="errors"></alcalde-vecinal-form>
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
                    descripcion: ''
                },
                errors: [],
                loading:false,
                cancel_url: route('alcalde-vecinal.index')
            }
        },
        mounted(){
            let id = this.getIDfromURL();

            axios.get(route('alcalde-vecinal.show', {id:id})).then((response) => {
                this.alcaldeVecinal = response.data.alcaldeVecinal;
            });

        },
        methods: {
            editNivelAgua(){
                this.loading = true;
                axios.put(route('alcalde-vecinal.update', {id:this.alcaldeVecinal.id}), this.alcaldeVecinal).then( (response) =>{
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
            },
            getIDfromURL(){
                let path = window.location.pathname;
                let segments = path.split("/");
                let id = null;

                for(let segment of segments)
                {
                    if(!isNaN(segment))
                    {
                        if(!id)
                        {
                            id = segment;
                        }
                    }
                }

                return id;
            }
        }
    }
</script>