@extends('plantilla.app')

@section('titulo', 'Control de Materiales')

@section('contenido')

<div class="max-w-6xl mx-auto px-6 mt-10 pb-16">
    <div class="bg-[#023047] rounded-2xl p-5 border border-gray-700 shadow-lg">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-white">Control de Materiales</h1>
                <p class="text-gray-400 text-xs mt-1">Registro de salida y devolución por turno</p>
            </div>
            <div class="flex items-center gap-2">
                <div id="step-indicator-1" class="flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full border bg-orange-500/10 border-orange-500/40 text-orange-400">
                    <span class="w-4 h-4 rounded-full bg-orange-500 text-white flex items-center justify-center text-[10px] font-bold">1</span>
                    Salida
                </div>
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <div id="step-indicator-2" class="flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full border bg-transparent border-gray-600 text-gray-500">
                    <span class="w-4 h-4 rounded-full bg-gray-600 text-gray-400 flex items-center justify-center text-[10px] font-bold">2</span>
                    Devolución
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="flex items-center gap-2 bg-green-500/10 border border-green-500/30 text-green-400 text-sm rounded-xl px-4 py-3 mb-5">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="flex items-center gap-2 bg-red-500/10 border border-red-500/30 text-red-400 text-sm rounded-xl px-4 py-3 mb-5">
                Revisa los campos marcados en rojo.
            </div>
        @endif

        {{-- PASO 1 --}}
        <div id="paso-1">
            <form method="POST" action="{{ route('prestamos.salida.store') }}" id="form-salida">
            @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1.5">Empleado <span class="text-orange-400">*</span></label>
                        <select name="id_empleado" id="sel-empleado"
                                class="w-full bg-[#012030] border border-gray-600 text-white text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="">— Selecciona empleado —</option>
                            @foreach($empleados as $empleado)
                                <option value="{{ $empleado->id_empleado }}"
                                        data-turno="{{ $empleado->turno }}"
                                        data-linea="{{ $empleado->linea_produccion }}"
                                        {{ old('id_empleado') == $empleado->id_empleado ? 'selected' : '' }}>
                                    #{{ $empleado->id_empleado }} – {{ $empleado->nombre }} {{ $empleado->apellido_p }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1.5">Turno</label>
                        <input type="text" name="turno" id="campo-turno" placeholder="Ej. Matutino"
                               value="{{ old('turno') }}"
                               class="w-full bg-[#012030] border border-gray-600 text-white text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1.5">Línea de producción</label>
                        <input type="text" name="linea_produccion" id="campo-linea" placeholder="Ej. Línea A"
                               value="{{ old('linea_produccion') }}"
                               class="w-full bg-[#012030] border border-gray-600 text-white text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>

                <div class="rounded-xl border border-gray-700 overflow-hidden mb-5">
                    <div class="bg-[#012030] px-4 py-3 flex items-center gap-2 border-b border-gray-700">
                        <span class="w-2 h-2 rounded-full bg-orange-400 inline-block"></span>
                        <span class="text-white text-sm font-semibold">Materiales disponibles</span>
                        <span class="ml-auto text-xs text-gray-500">Captura la cantidad que sale</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-[#012030] text-gray-400 text-xs uppercase tracking-widest">
                                    <th class="px-4 py-3 text-left">#</th>
                                    <th class="px-4 py-3 text-left">Material</th>
                                    <th class="px-4 py-3 text-left">Estatus</th>
                                    <th class="px-4 py-3 text-center">Exist. actual</th>
                                    <th class="px-4 py-3 text-center">Cant. salida</th>
                                    <th class="px-4 py-3 text-center">Exist. actualizada</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700/50">
                                @foreach($materiales as $i => $material)
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $i + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <span class="text-orange-400 font-semibold text-xs">#{{ $material->id_material }}</span>
                                        <p class="text-white font-medium text-sm mt-0.5">{{ $material->nombre_material }}</p>
                                        <input type="hidden" name="materiales[{{ $i }}][id_material]" value="{{ $material->id_material }}">
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $material->estatus === 'Disponible' ? 'bg-green-500/10 border border-green-500/30 text-green-400' : 'bg-red-500/10 border border-red-500/30 text-red-400' }}">
                                            {{ $material->estatus }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center text-gray-400 font-mono">
                                        {{ $material->existencia }}
                                        <input type="hidden" id="exist_{{ $i }}" value="{{ $material->existencia }}">
                                        <input type="hidden" name="materiales[{{ $i }}][existencia_antes]" value="{{ $material->existencia }}">
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <input type="number"
                                               name="materiales[{{ $i }}][cantidad_salida]"
                                               id="salida_{{ $i }}"
                                               min="0" max="{{ $material->existencia }}"
                                               value="0" data-idx="{{ $i }}"
                                               autocomplete="off"
                                               class="qty-salida w-24 text-center bg-[#012030] border border-gray-600 rounded-lg text-white text-sm px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span id="result_{{ $i }}" class="text-orange-400 font-bold font-mono text-base">{{ $material->existencia }}</span>
                                        <input type="hidden" name="materiales[{{ $i }}][existencia_actualizada]" id="result_hidden_{{ $i }}" value="{{ $material->existencia }}">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="bg-[#012030] border-t border-gray-700 px-4 py-3 flex items-center gap-6">
                        <div class="flex items-center gap-2">
                            <span class="text-gray-400 text-xs uppercase tracking-widest">Total materiales:</span>
                            <span class="text-white font-bold text-sm">{{ count($materiales) }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-gray-400 text-xs uppercase tracking-widest">Total en salida:</span>
                            <span class="text-orange-400 font-bold text-sm" id="total-salida">0</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('materiales.index') }}" class="border border-gray-600 text-gray-300 hover:text-white text-sm font-medium px-5 py-2.5 rounded-lg transition-colors">
                        Cancelar
                    </a>
                    <button type="button" onclick="irPaso2()"
                            class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition-colors">
                        Siguiente – Registrar devolución
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>{{-- /paso-1 --}}

        {{-- PASO 2 --}}
        <div id="paso-2" style="display:none;">
            <form method="POST" action="{{ route('prestamos.salida.store') }}" id="form-devolucion">
            @csrf
                <input type="hidden" name="id_empleado"      id="dev-empleado">
                <input type="hidden" name="turno"            id="dev-turno">
                <input type="hidden" name="linea_produccion" id="dev-linea">

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1.5">Empleado</label>
                        <input type="text" id="txt-empleado" readonly class="w-full bg-[#012030] border border-gray-600 text-gray-300 text-sm rounded-lg px-3 py-2.5">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1.5">Turno</label>
                        <input type="text" id="txt-turno" readonly class="w-full bg-[#012030] border border-gray-600 text-gray-400 text-sm rounded-lg px-3 py-2.5">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1.5">Línea</label>
                        <input type="text" id="txt-linea" readonly class="w-full bg-[#012030] border border-gray-600 text-gray-400 text-sm rounded-lg px-3 py-2.5">
                    </div>
                </div>

                <div class="rounded-xl border border-gray-700 overflow-hidden mb-5">
                    <div class="bg-[#012030] px-4 py-3 flex items-center gap-2 border-b border-gray-700">
                        <span class="w-2 h-2 rounded-full bg-orange-400 inline-block"></span>
                        <span class="text-white text-sm font-semibold">Devolución de materiales</span>
                        <span class="ml-auto text-xs text-gray-500">Captura el sobrante que regresa</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-[#012030] text-gray-400 text-xs uppercase tracking-widest">
                                    <th class="px-4 py-3 text-left">#</th>
                                    <th class="px-4 py-3 text-left">Material</th>
                                    <th class="px-4 py-3 text-center">Cant. que salió</th>
                                    <th class="px-4 py-3 text-center">Exist. antes</th>
                                    <th class="px-4 py-3 text-center">Cant. devuelta</th>
                                    <th class="px-4 py-3 text-center">Exist. final</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-dev" class="divide-y divide-gray-700/50"></tbody>
                        </table>
                    </div>
                    <div class="bg-[#012030] border-t border-gray-700 px-4 py-3 flex items-center gap-6">
                        <span class="text-gray-400 text-xs uppercase tracking-widest">Total devuelto:</span>
                        <span class="text-orange-400 font-bold text-sm ml-2" id="total-dev">0</span>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-3">
                    <button type="button" onclick="irPaso1()"
                            class="flex items-center gap-2 border border-gray-600 text-gray-300 hover:text-white text-sm font-medium px-5 py-2.5 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Regresar
                    </button>
                    <button type="button" onclick="guardar()"
                            class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Guardar registro completo
                    </button>
                </div>
            </form>
        </div>{{-- /paso-2 --}}

    </div>
</div>

{{-- JS inline para garantizar que carga independiente del @stack --}}
<script>
var listaMateriales = {!! json_encode($materiales->map(fn($m) => ['id' => $m->id_material, 'nombre' => $m->nombre_material, 'exist' => $m->existencia])->values()) !!};

// recalcular existencia actualizada en paso 1
document.querySelectorAll('.qty-salida').forEach(function(inp) {
    inp.addEventListener('input', function() {
        var i = this.dataset.idx;
        var e = parseFloat(this.value) || 0;
        var f = parseFloat(document.getElementById('exist_' + i).value) || 0;
        document.getElementById('result_' + i).textContent = f - e;
        document.getElementById('result_hidden_' + i).value = f - e;
        var total = 0;
        document.querySelectorAll('.qty-salida').forEach(function(x) { total += parseFloat(x.value) || 0; });
        document.getElementById('total-salida').textContent = total;
    });
});

// autocompletar turno y linea al elegir empleado
document.getElementById('sel-empleado').addEventListener('change', function() {
    var opt = this.options[this.selectedIndex];
    if (opt.dataset.turno) document.getElementById('campo-turno').value = opt.dataset.turno;
    if (opt.dataset.linea) document.getElementById('campo-linea').value = opt.dataset.linea;
});

function irPaso2() {
    var emp = document.getElementById('sel-empleado');
    if (!emp.value) { alert('Selecciona un empleado'); return; }

    document.getElementById('dev-empleado').value = emp.value;
    document.getElementById('dev-turno').value    = document.getElementById('campo-turno').value;
    document.getElementById('dev-linea').value    = document.getElementById('campo-linea').value;
    document.getElementById('txt-empleado').value = emp.options[emp.selectedIndex].text;
    document.getElementById('txt-turno').value    = document.getElementById('campo-turno').value || '—';
    document.getElementById('txt-linea').value    = document.getElementById('campo-linea').value || '—';

    // limpiar hiddens anteriores
    document.querySelectorAll('.js-mat-h').forEach(function(el) { el.remove(); });

    var tbody = document.getElementById('tbody-dev');
    tbody.innerHTML = '';
    var hayMat = false;

    listaMateriales.forEach(function(mat, i) {
        var cantSalida = parseFloat(document.getElementById('salida_' + i).value) || 0;
        if (cantSalida === 0) return;
        hayMat = true;
        var existAntes = mat.exist;
        var existTras  = existAntes - cantSalida;

        // copiar datos de salida al form-devolucion
        [['id_material', mat.id], ['cantidad_salida', cantSalida], ['existencia_antes', existAntes], ['existencia_actualizada', existTras]].forEach(function(par) {
            var h = document.createElement('input');
            h.type = 'hidden'; h.className = 'js-mat-h';
            h.name = 'materiales[' + i + '][' + par[0] + ']';
            h.value = par[1];
            document.getElementById('form-devolucion').appendChild(h);
        });

        var tr = document.createElement('tr');
        tr.className = 'hover:bg-white/5 transition-colors border-b border-gray-700/50';
        tr.innerHTML =
            '<td class="px-4 py-3 text-gray-500 text-xs">' + (i+1) + '</td>' +
            '<td class="px-4 py-3">' +
                '<span class="text-orange-400 font-semibold text-xs">#' + mat.id + '</span>' +
                '<p class="text-white font-medium text-sm mt-0.5">' + mat.nombre + '</p>' +
                '<input type="hidden" name="devolucion[' + i + '][id_material]" value="' + mat.id + '">' +
                '<input type="hidden" name="devolucion[' + i + '][existencia_antes]" value="' + existAntes + '">' +
            '</td>' +
            '<td class="px-4 py-3 text-center text-gray-400 font-mono">' + cantSalida + '</td>' +
            '<td class="px-4 py-3 text-center text-gray-400 font-mono">' + existAntes + '</td>' +
            '<td class="px-4 py-3 text-center">' +
                '<input type="number" name="devolucion[' + i + '][cantidad_devuelta]" id="dev_' + i + '" min="0" max="' + cantSalida + '" value="0" data-et="' + existTras + '" data-i="' + i + '" class="qty-dev w-24 text-center bg-[#012030] border border-gray-600 rounded-lg text-white text-sm px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-orange-500">' +
            '</td>' +
            '<td class="px-4 py-3 text-center">' +
                '<span id="gf_' + i + '" class="text-orange-400 font-bold font-mono text-base">' + existTras + '</span>' +
                '<input type="hidden" name="devolucion[' + i + '][existencia_final]" id="gf_h_' + i + '" value="' + existTras + '">' +
            '</td>';
        tbody.appendChild(tr);
    });

    if (!hayMat) {
        tbody.innerHTML = '<tr><td colspan="6" class="px-4 py-6 text-center text-gray-500 text-sm">No se registró salida de ningún material</td></tr>';
    }

    // listeners devolución
    document.querySelectorAll('.qty-dev').forEach(function(inp) {
        inp.addEventListener('input', function() {
            var i  = this.dataset.i;
            var d  = parseFloat(this.value) || 0;
            var et = parseFloat(this.dataset.et) || 0;
            var gf = et + d;
            document.getElementById('gf_' + i).textContent = gf;
            document.getElementById('gf_h_' + i).value = gf;
            var total = 0;
            document.querySelectorAll('.qty-dev').forEach(function(x) { total += parseFloat(x.value) || 0; });
            document.getElementById('total-dev').textContent = total;
        });
    });

    document.getElementById('paso-1').style.display = 'none';
    document.getElementById('paso-2').style.display = 'block';
    document.getElementById('step-indicator-1').querySelector('span').className = 'w-4 h-4 rounded-full bg-gray-600 text-gray-400 flex items-center justify-center text-[10px] font-bold';
    document.getElementById('step-indicator-2').querySelector('span').className = 'w-4 h-4 rounded-full bg-orange-500 text-white flex items-center justify-center text-[10px] font-bold';
}

function irPaso1() {
    document.getElementById('paso-2').style.display = 'none';
    document.getElementById('paso-1').style.display = 'block';
    document.getElementById('step-indicator-2').querySelector('span').className = 'w-4 h-4 rounded-full bg-gray-600 text-gray-400 flex items-center justify-center text-[10px] font-bold';
    document.getElementById('step-indicator-1').querySelector('span').className = 'w-4 h-4 rounded-full bg-orange-500 text-white flex items-center justify-center text-[10px] font-bold';
}

function guardar() {
    document.getElementById('form-devolucion').submit();
}
</script>

@endsection