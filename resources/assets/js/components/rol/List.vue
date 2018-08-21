<template>
    <div class="row">
        <div class="col-sm-12 table-responsive">
            <table class="table table bordered">
                <thead>
                    <tr>
                        <th class="number-col">#</th>
                        <th>
                            Descripción
                        </th>
                        <th class="action-col">
                            
                        </th>
                    </tr>
                    <tr v-for="(rol, index) in roles">
                        <td>
                            {{index + 1}}
                        </td>
                        <td>
                            {{rol.descripcion}}
                        </td>
                        <td>
                            <a :href="getEditUrl(rol.id)" class="btn btn-primary">
                                Editar
                            </a>
                            <a href="#" class="btn btn-danger" v-on:click="deleteTipoPersona(rol)">
                                Eliminar
                            </a>
                        </td>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                roles: []
            }
        },
        mounted() {
            this.getRoles();
        },
        methods: {

            deleteTipoPersona(rol){
                let r = confirm('Estas seguro de eliminar este registro');

                if(r === true)
                {
                    axios.delete(route('rol.destroy',{id:rol.id} )).then((response)=>{
                        if(response.data.success){
                            this.getRoles();
                        }else{
                            alert(response.data.message);
                        }
                    }).catch((error)=>{
                        console.log(error.data);
                    });
                }
            },
            getRoles(){
                axios.get(route('rol.all')).then((response) => {
                    this.roles = response.data.roles;
                });
            },
            getEditUrl(id){
                return route('rol.edit',{id:id})
            }
        }
    }
</script>