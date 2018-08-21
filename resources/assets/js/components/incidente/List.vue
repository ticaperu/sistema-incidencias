<template>
    <div class="row">
        <div class="col-sm-12 table-responsive">
            <div class="row">
                <div class="col-sm-3">
                    <label for="fecha">
                        Fecha
                    </label>
                    <datepicker v-model="fecha" :input-class="'form-control'" id="fecha"
                                :language="languages['es']" :format="'dd/MM/yyyy'" @input="searchFilter()"
                                :typeable="true"
                    ></datepicker>
                </div>
                <div class="col-sm-3">
                    <label for="territorio_vecinal_id">Territorios Vecinales</label>
                    <select name="territorio_vecinal_id" id="territorio_vecinal_id" class="form-control"
                            v-model="territorio_vecinal_id" v-on:change="searchFilter()">
                        <option value="">Selecione..</option>
                        <option v-for="territorioVecinal in territoriosVecinales" :value="territorioVecinal.id">
                            {{territorioVecinal.descripcion}}
                        </option>
                    </select>
                </div>
                <div class="col-sm-3">
                    <label for="urbanizacion_id">Urbanización</label>
                    <select name="urbanizacion_id" id="urbanizacion_id" class="form-control"
                            v-model="urbanizacion_id" v-on:change="searchFilter()">
                        <option value="">Selecione..</option>
                        <option v-for="urbanizacion in urbanizaciones" :value="urbanizacion.id">
                            {{urbanizacion.descripcion}}
                        </option>
                    </select>
                </div>
                <div class="col-sm-3">
                    <label for="estado_incidente_s_id">Estado</label>
                    <select name="estado_incidente_id" id="estado_incidente_s_id" class="form-control"
                            v-model="estado_incidente_id" v-on:change="searchFilter()">
                        <option value="">Selecione..</option>
                        <option v-for="estadoIncidente in estadosIncidentes" :value="estadoIncidente.id">
                            {{estadoIncidente.descripcion}}
                        </option>
                    </select>
                </div>
            </div>
            <br>
            <table class="table table bordered">
                <thead>
                <tr>
                    <th class="number-col">#</th>
                    <th>
                        Fecha
                    </th>
                    <th>
                        Territorio Vecinal
                    </th>
                    <th>
                        Urbanización
                    </th>
                    <th>
                        Descripción
                    </th>
                    <th>
                        Estado de atención
                    </th>
                    <th>

                    </th>
                </tr>
                <tr v-for="(incidente, index) in incidentes">
                    <td>
                        {{index + 1}}
                    </td>
                    <td>
                        {{incidente.fecha}}
                    </td>
                    <td>
                        {{incidente.urbanizacion.territorio_vecinal.descripcion}}
                    </td>
                    <td>
                        {{incidente.urbanizacion.descripcion}}
                    </td>
                    <td>
                        {{incidente.descripcion}}
                    </td>
                    <td>
                        <span class="badge badge-secondary" :style="'background: ' + incidente.estado_incidente.color">{{incidente.estado_incidente.descripcion}}</span>
                    </td>
                    <td>
                        <a :href="getEditUrl(incidente.id)" class="btn btn-primary" >
                            Detalle
                        </a>
                    </td>
                </tr>
                </thead>
            </table>
        </div>

        <div class="modal fade" id="mapModal" tabindex="-1" role="dialog" aria-labelledby="mapModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Mapa</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-9">
                                <div id="map" style="height: 250px; width: 100%">

                                </div>
                            </div>
                            <div class="col-sm-3">
                                <a href="#" class="btn btn-success" v-on:click="addPolyline()"> Agregar Linea</a>
                                <br>
                                <br>
                                <ul class="list-group">
                                    <li class="list-group-item" v-for="(polyline, index) in polylines">
                                        <a href="#">
                                            <i class="fas fa-circle" :style="'color:'+ polyline.color"></i>
                                        </a>

                                        <a href="#" class="text-danger float-right"
                                           v-on:click="removeLine(polyline, index)">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" :disabled="loading"
                                v-on:click="updateIncidente()">Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalState" tabindex="-1" role="dialog" aria-labelledby="modalState"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalStateLabel">Cambiar de Estado</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="estado_incidente_id">Estado</label>
                                    <select name="estado_incidente_id" id="estado_incidente_id" class="form-control"
                                            v-model="selectedIncidente.estado_incidente_id">
                                        <option value="">Selecione..</option>
                                        <option v-for="estadoIncidente in estadosIncidentes"
                                                :value="estadoIncidente.id">
                                            {{estadoIncidente.descripcion}}
                                        </option>
                                    </select>
                                    <small class="form-text text-danger" v-if="errors.estado_incidente_id"
                                           v-for="error in errors.estado_incidente_id">
                                        {{error}}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" :disabled="loading"
                                v-on:click="updateIncidente()">Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalCoordinacion" tabindex="-1" role="dialog" aria-labelledby="modalCoordinacion"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCoordinacionLabel">Registrar Coordinaciones</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="estado_incidente_id">Coordinaciones</label>
                                    <select name="coordinacion_id" id="coordinacion_id" class="form-control"
                                            v-model="coordinacion_id">
                                        <option value="">Selecione..</option>
                                        <option v-for="coordinacion in coordinacions" :value="coordinacion.id">
                                            {{coordinacion.descripcion}}
                                        </option>
                                    </select>
                                    <small class="form-text text-danger" v-if="errors.coordinacion_id"
                                           v-for="error in errors.coordinacion_id">
                                        {{error}}
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label for="descripcion">Descripción</label>
                                    <textarea name="descripcion" id="descripcion" rows="3" v-model="atencion_descripcion"
                                              class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" :disabled="loading"
                                v-on:click="registrarCoordinacion()">Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';
    import * as lang from "vuejs-datepicker/src/locale";
    import * as moment from 'moment'
    export default {
        components: {
            Datepicker
        },
        data() {
            return {
                incidentes: [],
                map: null,
                marker: null,
                selectedIncidente: {},
                polgygon: null,
                polylines: [],
                loading: false,
                estadosIncidentes: [],
                coordinacions: [],
                errors: [],
                coordinacion_id: '',
                atencion_descripcion: '',
                languages: lang,
                fecha:'',
                territoriosVecinales: [],
                urbanizaciones: [],
                estado_incidente_id:'',
                urbanizacion_id:'',
                territorio_vecinal_id:''
            }
        },
        mounted() {
            axios.get(route('estado-incidente.all')).then((response) => {
                this.estadosIncidentes = response.data.estadosIncidentes;
            });

            axios.get(route('coordinacion.all')).then((response) => {
                this.coordinacions = response.data.coordinacions;
            });

            axios.get(route('urbanizacion.all')).then((response) => {
                this.urbanizaciones = response.data.urbanizaciones;
            });

            axios.get(route('territorio-vecinal.all')).then((response) => {
                this.territoriosVecinales = response.data.territoriosVecinales;
            });

            this.getPersonas();

            $('#mapModal').on('shown.bs.modal', () => {

                this.polylines = [];

                this.map = new GMaps({
                    div: '#map',
                    zoom: 17,
                    lat: this.selectedIncidente.latitud ? this.selectedIncidente.latitud : -12.043333,
                    lng: this.selectedIncidente.latitud ? this.selectedIncidente.longitud : -77.028333
                });


                this.marker = this.map.addMarker({
                    lat: this.selectedIncidente.latitud ? this.selectedIncidente.latitud : -12.043333,
                    lng: this.selectedIncidente.longitud ? this.selectedIncidente.longitud : -77.028333,
                    draggable: true,
                    animation: google.maps.Animation.DROP
                });

                this.setMapArea();

                this.marker.addListener('dragend', (event) => {

                    let isWithinPolygon = google.maps.geometry.poly.containsLocation(event.latLng, this.polygon);

                    if (isWithinPolygon) {
                        this.map.setCenter(this.marker.getPosition().lat(), this.marker.getPosition().lng());
                        let myLatlng = new google.maps.LatLng(this.map.getCenter().lat(), this.map.getCenter().lng());
                        this.marker.setPosition(myLatlng);
                    } else {
                        let myLatlng = new google.maps.LatLng(this.selectedIncidente.urbanizacion.latitude, this.selectedIncidente.urbanizacion.longitude);
                        this.map.setCenter(this.selectedIncidente.urbanizacion.latitude, this.selectedIncidente.urbanizacion.longitude);
                        this.marker.setPosition(myLatlng);
                    }

                    this.selectedIncidente.latitud = this.marker.getPosition().lat();
                    this.selectedIncidente.longitud = this.marker.getPosition().lng();

                });

                for (let polyline of this.selectedIncidente.polylines) {
                    let color = this.getRandomColor();

                    let g_polyline = this.map.drawPolyline({
                        path: polyline.coordinates,
                        strokeColor: color,
                        strokeOpacity: 1,
                        strokeWeight: 6,
                        editable: true
                    });

                    this.polylines.push({color: color, polyline: g_polyline});

                }

            });

        },
        methods: {
            deleteTipoPersona(incidente) {
                let r = confirm('Estas seguro de eliminar este registro');

                if (r === true) {
                    axios.delete(route('incidente.destroy', {id: incidente.id})).then((response) => {
                        if (response.data.success) {
                            this.getPersonas();
                        } else {
                            alert(response.data.message);
                        }
                    }).catch((error) => {
                        console.log(error.data);
                    });
                }
            },
            getPersonas() {
                axios.get(route('incidente.all', {date:this.fecha,urbanizacion: this.urbanizacion_id, estado: this.estado_incidente_id, territorio: this.territorio_vecinal_id})).then((response) => {
                    this.incidentes = response.data.incidentes;
                });
            },
            getEditUrl(id) {
                return route('incidente.detalle', {id: id});
            },
            setMap(row) {
                this.coordinacion_id = '';
                this.atencion_descripcion = '';
                this.selectedIncidente = row;
            },
            setMapArea() {
                if (this.polygon) {
                    this.polygon.setMap(null);
                }

                this.polygon = this.map.drawPolygon({
                    paths: this.selectedIncidente.urbanizacion.coordenadas,
                    strokeColor: '#e9e513',
                    strokeOpacity: 1,
                    strokeWeight: 3,
                    fillColor: '#da151b',
                    fillOpacity: 0.6
                });
            },
            getRandomColor() {
                var letters = '0123456789ABCDEF';
                var color = '#';
                for (var i = 0; i < 6; i++) {
                    color += letters[Math.floor(Math.random() * 16)];
                }
                return color;
            },
            addPolyline() {
                let path = [
                    this.selectedIncidente.urbanizacion.coordenadas[0],
                    this.selectedIncidente.urbanizacion.coordenadas[1]
                ];

                let color = this.getRandomColor();

                let polyline = this.map.drawPolyline({
                    path: path,
                    strokeColor: color,
                    strokeOpacity: 1,
                    strokeWeight: 6,
                    editable: true
                });

                this.polylines.push({color: color, polyline: polyline});
            },
            updateIncidente() {
                let polylines = [];

                for (let polyline of this.polylines) {
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

                this.selectedIncidente.polylines = polylines;

                this.loading = true;
                axios.put(route('incidente.update', {id: this.selectedIncidente.id}), this.selectedIncidente).then((response) => {
                    $('#mapModal').modal('hide');
                    $('#modalState').modal('hide');
                    this.getPersonas();
                    this.loading = false;
                }).catch((error) => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    }
                    this.loading = false;
                })
            },
            removeLine(polyline, index) {
                polyline.polyline.setMap(null);
                this.polylines.splice(index, 1);
            },
            registrarCoordinacion() {
                this.loading = true;
                axios.post(route('incidente.registrar-coordinacion', {id: this.selectedIncidente.id}),
                    {
                        coordinacion_id: this.coordinacion_id,
                        descripcion: this.atencion_descripcion
                    }
                ).then((response) => {
                    $('#modalCoordinacion').modal('hide');
                    this.loading = false;
                }).catch((error) => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    }
                    this.loading = false;
                })
            },
            searchFilter(){
                this.fecha = moment(this.fecha).format('DD/MM/YYYY');
                if(this.fecha == 'Invalid date')
                {
                    this.fecha = '';
                }
                this.getPersonas();
            }
        }
    }
</script>