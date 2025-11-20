@if (!empty($kpi_cards))
    @foreach($kpi_cards as $card)
        <div class="col-xxxl-4 col-xl-4 col-lg-6 col-md-6 col-12">
            <div class="box mb-20 h-100">
                <div class="box-body d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        @if(!empty($card['icon']))
                            <img src="{{ asset($card['icon']) }}" alt="" class="w-120" />
                        @endif
                        <div>
                            <h4 class="mb-5 text-muted text-uppercase fs-12 letter-spacing-1">{{ $card['title'] }}</h4>
                            <h2 class="mb-0 fw-600">{{ is_numeric($card['value']) ? number_format((float) $card['value']) : $card['value'] }}</h2>
                            @if(!empty($card['description']))
                                <p class="mb-0 text-muted fs-12">{{ $card['description'] }}</p>
                            @endif
                        </div>
                    </div>
                    @if(!empty($card['tag']))
                        <span class="badge bg-light text-primary fw-500 px-3 py-2">{{ $card['tag'] }}</span>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
@endif
