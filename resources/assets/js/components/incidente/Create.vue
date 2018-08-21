<template>
    <div class="card">
        <div class="card-header">
            Crear Incidente
        </div>
        <form action="" v-on:submit.prevent="newIncidente()">
            <incidente-form :incidente="incidente"  :errors="errors"></incidente-form>

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
                incidente: {
                    state:'Activo',
                    tipo_incidente_id:'',
                    estado_incidente_id: '',
                    urbanizacion_id: '',
                    src_imagen:'',
                    calle_obstaculo: {},
                    inundacion:{},
                    polylines:[]
                },
                errors: [],
                loading: false,
                polygon:{},
                cancel_url:route('incidente.index')
            }
        },
        methods: {
            newIncidente() {

                let polylines = [];

                for (let polyline of this.incidente.polylines) {
                    let len = polyline.polyline.getPath().getLength();
                    let coordinates = "";
                    for (var i = 0; i < len; i++) {
                        if (i == (len - 1)) {
                            coordinates += polyline.polyline.getPath().getAt(i).toUrlValue(5);
                        } else {
                            coordinates += polyline.polyline.getPath().getAt(i).toUrlValue(5) + ";";
                        }
                    }

                    polylines.push(coordinates);
                }

                this.incidente.polylines = polylines;


                this.loading = true;
                axios.post(route('incidente.store'), this.incidente).then((response) => {
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