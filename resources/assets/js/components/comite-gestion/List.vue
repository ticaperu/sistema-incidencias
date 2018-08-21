<template>
    <div class="row">
        <div class="col-sm-12 table-responsive">
            <table class="table table bordered">
                <thead>
                    <tr>
                        <th class="number-col">#</th>
                        <th>
                            Persona
                        </th>
                        <th>
                            Territorio Vecinal
                        </th>
                        <th class="action-col">
                            
                        </th>
                    </tr>
                    <tr v-for="(comiteGestion, index) in comitesGestion">
                        <td>
                            {{index + 1}}
                        </td>
                        <td>
                            {{comiteGestion.persona.nombres}} {{comiteGestion.persona.ape_paterno}} {{comiteGestion.persona.ape_materno}}
                        </td>
                        <td>
                            {{comiteGestion.territorio_vecinal.descripcion}}
                        </td>
                        <td>
                            <a :href="getEdiUrl(comiteGestion.id)" class="btn btn-primary">
                                Editar
                            </a>
                            <a href="#" class="btn btn-danger" v-on:click="deleteComiteGestion(comiteGestion)">
                                Baja
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
                comitesGestion: []
            }
        },
        mounted() {
            this.getNivelesAgua();
        },
        methods: {

            deleteComiteGestion(comiteGestion){
                let r = confirm('Estas seguro de dar de baja este registro');

                if(r === true)
                {
                    axios.delete(route('comite-gestion.destroy', {id: comiteGestion.id})).then((response)=>{
                        if(response.data.success){
                            this.getNivelesAgua();
                        }else{
                            alert(response.data.message);
                        }
                    }).catch((error)=>{
                        console.log(error.data);
                    });
                }
            },
            getNivelesAgua(){
                axios.get(route('comite-gestion.all')).then((response) => {
                    this.comitesGestion = response.data.comitesGestion;
                });
            },
            getEdiUrl(id){
                return route('comite-gestion.edit', {id: id})
            }

        }
    }
</script>