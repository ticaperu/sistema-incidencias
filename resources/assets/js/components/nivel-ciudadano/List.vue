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
                        <th>
                            Puntos
                        </th>
                        <th class="action-col">
                            
                        </th>
                    </tr>
                    <tr v-for="(nivelCiudadano, index) in nivelesCiudadanos">
                        <td>
                            {{index + 1}}
                        </td>
                        <td>
                            {{nivelCiudadano.descripcion}}
                        </td>
                        <td>
                            {{nivelCiudadano.total_minimo}} - {{nivelCiudadano.total_maximo}}
                        </td>
                        <td>
                            <a :href="getEditUrl(nivelCiudadano.id)" class="btn btn-primary">
                                Editar
                            </a>
                            <a href="#" class="btn btn-danger" v-on:click="deleteNivelCudadano(nivelCiudadano)">
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
                nivelesCiudadanos: []
            }
        },
        mounted() {
            this.getNivelesCiudadanos();
        },
        methods: {

            deleteNivelCudadano(nivelCiudadano){
                let r = confirm('Estas seguro de eliminar este registro');

                if(r === true)
                {
                    axios.delete(route('nivel-ciudadano.destroy', {id:nivelCiudadano.id})).then((response)=>{
                        if(response.data.success){
                            this.getNivelesCiudadanos();
                        }else{
                            alert(response.data.message);
                        }
                    }).catch((error)=>{
                        console.log(error.data);
                    });
                }
            },
            getNivelesCiudadanos(){
                axios.get(route('ciudadano.all')).then((response) => {
                    this.nivelesCiudadanos = response.data.nivelesCiudadanos;
                });
            },
            getEditUrl(id){
                return route('nivel-ciudadano.edit', {id:id});
            }

        }
    }
</script>