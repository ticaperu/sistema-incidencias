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
                    <tr v-for="(territorioVecinal, index) in territoriosVecinales">
                        <td>
                            {{index + 1}}
                        </td>
                        <td>
                            {{territorioVecinal.descripcion}}
                        </td>
                        <td>
                            <a :href="getEditUrl(territorioVecinal.id)" class="btn btn-primary">
                                Editar
                            </a>
                            <a href="#" class="btn btn-danger" v-on:click="deleteTipoPersona(territorioVecinal)">
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
                territoriosVecinales: []
            }
        },
        mounted() {
            this.getTerritoriosVecinales();
        },
        methods: {

            deleteTipoPersona(territorioVecinal){
                let r = confirm('Estas seguro de eliminar este registro');

                if(r === true)
                {
                    axios.delete(route('territorio-vecinal.destroy', {id:territorioVecinal.id})).then((response)=>{
                        if(response.data.success){
                            this.getTerritoriosVecinales();
                        }else{
                            alert(response.data.message);
                        }
                    }).catch((error)=>{
                        console.log(error.data);
                    });
                }
            },
            getTerritoriosVecinales(){
                axios.get(route('territorio-vecinal.all')).then((response) => {
                    this.territoriosVecinales = response.data.territoriosVecinales;
                });
            },
            getEditUrl(id){
                return route('territorio-vecinal.edit', {id:id});
            }

        }
    }
</script>