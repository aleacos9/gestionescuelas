<?php
class eiformulario extends gestionescuelas_ei_formulario
{
	function extender_objeto_js()
	{
		$importe_mensual_cuota_x_grado = dao_consultas::catalogo_de_parametros("importe_mensual_cuota_x_grado");
		
		echo "
		{$this->objeto_js}.ini = function()
		{
			var importe_mensual_cuota_x_grado = '{$importe_mensual_cuota_x_grado}';
			if (importe_mensual_cuota_x_grado == 'SI'){
				this.ef('grado').mostrar();
			} else {
				this.ef('grado').ocultar();
			}
			
			this.ef('id_persona').ocultar();
			this.ef('cuota').ocultar();
			this.ef('cargo_a_generar').set_obligatorio(1);
		}
		
		//---- Procesamiento de EFs --------------------------------
		{$this->objeto_js}.evt__forma_generacion__procesar = function(es_inicial)
		{
		    /*if (this.ef('forma_generacion').get_estado() == 'I') {
			    this.ef('id_persona').mostrar();
			    this.ef('id_persona').set_obligatorio(1);
			} else {
			    alert('Tenga en cuenta que al seleccionar la opción Grupal se le generará el costo a todos los alumnos activos cargados en el sistema');
			    this.ef('id_persona').set_obligatorio(0);
			    this.ef('id_persona').resetear_estado();
			    this.ef('id_persona').ocultar();
			}*/
			
			if (!es_inicial) {
			    if (this.ef('forma_generacion').get_estado() == 'G') {
			        alert('Tenga en cuenta que al seleccionar la opción Grupal se le generará el costo a todos los alumnos activos cargados en el sistema');
			        this.ef('id_persona').set_obligatorio(0);
			        this.ef('id_persona').resetear_estado();
			        this.ef('id_persona').ocultar();
			    } else {
			        this.ef('id_persona').mostrar();
			        this.ef('id_persona').set_obligatorio(1);
			    }
			}
		}
		
		{$this->objeto_js}.evt__cargo_a_generar__procesar = function(es_inicial)
		{
		    this.ef('anio').resetear_estado();
		    
		    switch(this.ef('cargo_a_generar').get_estado())
			{
				case '1': //inscripción anual
				    this.ef('anio').set_obligatorio(1);
				    this.ef('cuota').set_obligatorio(0);
				    this.ef('cuota').resetear_estado();
			        this.ef('cuota').ocultar();
				    break;
				case '2': //cuota mensual
				    this.ef('cuota').mostrar();
			        this.ef('cuota').set_obligatorio(1);
			        this.ef('anio').set_obligatorio(1);
			        break;
			}
		}
		";
	}


}
?>