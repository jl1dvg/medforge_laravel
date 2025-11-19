@php
    use Illuminate\Support\Str;
@endphp

@if(!empty($eventos))
    <section class="cd-horizontal-timeline mt-3">
        <div class="timeline">
            <div class="events-wrapper">
                <div class="events">
                    <ol style="white-space: nowrap; overflow-x: auto; display: flex; gap: 1rem;">
                        @foreach($eventos as $index => $evento)
                            @php
                                $eventoData = is_array($evento) ? $evento : (array) $evento;
                                $fechaRaw = $eventoData['fecha'] ?? null;
                                $timestamp = $fechaRaw && strtotime($fechaRaw) ? strtotime($fechaRaw) : null;
                                $fechaData = $timestamp ? date('d/m/Y', $timestamp) : '01/01/2000';
                                $textoFecha = $timestamp ? date('d M', $timestamp) : '01 Jan';
                            @endphp
                            <li style="min-width: 80px; text-align: center;">
                                <a href="#0"
                                   class="{{ $index === 0 ? 'selected' : '' }}"
                                   data-date="{{ $fechaData }}"
                                   style="display: inline-block; padding: 6px 10px;">
                                    {{ $textoFecha }}
                                </a>
                            </li>
                        @endforeach
                    </ol>
                    <span class="filling-line" aria-hidden="true"></span>
                </div>
            </div>
            <ul class="cd-timeline-navigation">
                <li><a href="#0" class="prev inactive">Prev</a></li>
                <li><a href="#0" class="next">Next</a></li>
            </ul>
        </div>
        <div class="events-content">
            <ol>
                @foreach($eventos as $index => $evento)
                    @php
                        $eventoData = is_array($evento) ? $evento : (array) $evento;
                        $procedimiento = $eventoData['procedimiento_proyectado'] ?? '';
                        $partes = explode(' - ', $procedimiento);
                        $nombre = implode(' - ', array_slice($partes, 2));
                        $contenido = $eventoData['contenido'] ?? '';
                        $fechaMostrar = $eventoData['fecha'] ?? null;
                    @endphp
                    <li data-date="{{ $fechaMostrar ? \Carbon\Carbon::parse($fechaMostrar)->format('d/m/Y') : '' }}"
                        class="{{ $index === 0 ? 'selected' : '' }}">
                        <h2>{{ $nombre ?: $procedimiento }}</h2>
                        <small>{{ $fechaMostrar ? \Carbon\Carbon::parse($fechaMostrar)->format('F jS, Y') : '—' }}</small>
                        <hr class="my-30">
                        <p class="pb-30">{!! nl2br(e($contenido)) !!}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>
@else
    <p class="text-muted mb-0">No hay datos disponibles para mostrar en el timeline.</p>
@endif
