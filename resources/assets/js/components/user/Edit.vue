<template>
    <div class="card">
        <div class="card-header">
            Editar Usuario
        </div>
        <form action="" v-on:submit.prevent="editTipoUser()">
            <div class="card-body">
                <user-form v-if="user.id" :user="user" :errors="errors"></user-form>
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
                user: {
                },
                errors: [],
                loading:false,
                cancel_url:route('user.index')
            }
        },
        mounted(){
            let id = this.getIDfromURL();

            axios.get(route('user.show',{id:id})).then((response) => {
                this.user = response.data.user;
            });

        },
        methods: {
            editTipoUser(){
                this.loading = true;
                axios.put(route('user.update', {id:this.user.id}), this.user).then( (response) =>{
                    if(response.data['success'])
                    {
                        window.location.href = this.cancel_url;
                    }
                    else
                    {
                        alert(response.data['message']);
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