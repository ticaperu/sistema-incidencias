<template>
    <div class="row">
        <div class="col-sm-12 table-responsive">
            <table class="table table bordered">
                <thead>
                    <tr>
                        <th class="number-col">#</th>
                        <th>
                            Nombre
                        </th>
                        <th>
                            Territorio Vecinal
                        </th>
                        <th class="action-col">
                            
                        </th>
                    </tr>
                    <tr v-for="(urbanizacion, index) in urbanizaciones">
                        <td>
                            {{index + 1}}
                        </td>
                        <td>
                            {{urbanizacion.descripcion}}
                        </td>
                        <td>
                            {{urbanizacion.territorio_vecinal.descripcion}}
                        </td>
                        <td>
                            <a :href="getEditUrl(urbanizacion.id)" class="btn btn-primary">
                                Editar
                            </a>
                            <a href="#" class="btn btn-danger" v-on:click="deleteTipoPersona(urbanizacion)">
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
                urbanizaciones: []
            }
        },
        mounted() {
            this.getUrbanizaciones();
        },
        methods: {

            deleteTipoPersona(urbanizacion){
                let r = confirm('Estas seguro de eliminar este registro');

                if(r === true)
                {
                    axios.delete(route('urbanizacion.destroy', {id:urbanizacion.id})).then((response)=>{
                        if(response.data.success){
                            this.getUrbanizaciones();
                        }else{
                            alert(response.data.message);
                        }
                    }).catch((error)=>{
                        console.log(error.data);
                    });
                }
            },
            getUrbanizaciones(){
                axios.get(route('urbanizacion.all')).then((response) => {
                    this.urbanizaciones = response.data.urbanizaciones;
                });
            },
            getEditUrl(id){
                return route('urbanizacion.edit',{id:id});
            }
        }
    }
</script>