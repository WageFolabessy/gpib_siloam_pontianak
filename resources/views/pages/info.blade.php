@extends('components.main') {{-- Pastikan path ini benar --}}

@section('title')
    - Info
@endsection

@section('style')
    <style>
        .highlight {
            background-color: #fff3cd !important;
            border-radius: 0.25rem;
            box-shadow: 0 0 5px rgba(255, 193, 7, 0.5) !important;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .speakable {
            cursor: pointer;
        }

        .card-body ol,
        .card-body ul {
            text-align: left;
            padding-left: 2rem;
        }

        .card-body li {
            margin-bottom: 0.5rem;
        }

        .card {
            overflow: hidden;
        }
    </style>
@endsection

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center gy-4">

            {{-- Informasi Sejarah --}}
            <div class="col-md-10 col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body speakable">
                        <h4 class="card-title text-center">Sejarah</h4>
                        <p class="card-text text-justify">
                            "SILOAM" adalah nama Jemaat yang ditetapkan berdasarkan Surat
                            Penjerahan, tertanggal Pontianak 29 September 1963 dari Panitia
                            Pembangunan Ibadat Pontianak yang disebut Panitia 9 menyerahkan
                            kepada Madjelis Sinode GPIB Djakarta yang menjaksikan Madjelis
                            Geredja GPIB Pontianak, sesuai data histories bernama De
                            Protestansche Gemeente te Pontianak atau Jemaat Protestan di
                            Pontianak menjadi GPIB Jemaat "SILOAM" Pontianak. Perlu kiranya
                            diketahui bahwa pada masa peralihan dari De Indishe Kerk (DIK)
                            kepada GEREJA PROTESTAN di INDONESIA bagian BARAT pada tahun
                            1948 terakhir kali dikuatkan dengan Surat Keputusan Menteri
                            Dalam Negeri RI No. SK. 22//DDA/1969/D/13, tanggal Jakarta
                            20-3-1978.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Informasi Visi --}}
            <div class="col-md-10 col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body speakable">
                        <h4 class="card-title text-center">Visi</h4>
                        <p class="card-text text-center">
                            Gereja GPIB Jemaat SILOAM Pontianak memiliki visi untuk menjadi
                            Gereja yang mewujudkan damai sejahtera Allah bagi seluruh
                            ciptaan-Nya, serta berperan aktif dalam pelayanan sosial,
                            pendidikan, dan lingkungan.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Informasi Misi --}}
            <div class="col-md-10 col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body speakable">
                        <h4 class="card-title text-center">Misi</h4>
                        <ol>
                            <li>Menjadi Gereja yang terus menerus diperbaharui dengan bertolak dari firman Allah yang
                                terwujud dalam perilaku warga gereja, baik dalam Persekutuan maupun dalam hidup
                                bermasyarakat.</li>
                            <li>Menjadi Gereja yang hadir sebagai contoh kehidupan, yang terwujud melalui inisiatif dan
                                partisipasi dalam kesetiakawanan sosial serta kerukunan dalam Masyarakat, dengan berbasis
                                pada perilaku kehidupan keluarga yang kuat dan sejahtera.</li>
                            <li>Menjadi Gereja yang membangun keutuhan ciptaan yang terwujud melalui perhatian terhadap
                                lingkungan hidup, semangat keesaan dan semangat persatuan dan kesatuan warga Gereja sebagai
                                warga masyarakat.</li>
                        </ol>
                    </div>
                </div>
            </div>

            {{-- Info Pendeta --}}
            <div class="col-md-10 col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body speakable">
                        <h4 class="card-title">Pendeta yang Melayani Saat Ini</h4>
                        <ul>
                            @php
                                $foundKMJ = false;
                                $foundPJ = [];
                            @endphp
                            @forelse ($pengurus as $item)
                                @if ($item->kategori == 'Ketua Majelis Jemaat' && !$foundKMJ)
                                    <li><strong>Ketua Majelis Jemaat:</strong> {{ $item->nama }}@php $foundKMJ = true; @endphp</li>
                                @elseif ($item->kategori == 'Pendeta Jemaat')
                                    @php $foundPJ[] = $item->nama; @endphp
                                @endif
                            @empty
                                <li>Data pendeta tidak tersedia.</li>
                            @endforelse
                            @if (!empty($foundPJ))
                                <li><strong>Pendeta Jemaat:</strong> {{ implode(', ', $foundPJ) }}</li>
                            @endif
                            @if (!$foundKMJ && empty($foundPJ) && $pengurus->count() > 0)
                                <li>Data kategori pendeta tidak sesuai.</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Wilayah Pelayanan --}}
            <div class="col-md-10 col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body speakable">
                        <h4 class="card-title">Wilayah Pelayanan</h4>
                        <ul>
                            <li>GPIB Jemaat "Siloam" Pontianak, Kota Pontianak</li>
                            <li>Bajem Tuah Petara Sintang, Kabupaten Sintang</li>
                            <li>Pos Pelkes Ekklesia Nanga Silat, Kabupaten Kapuas Hulu</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/pages/js/speechsynthesis/info.js') }}"></script>
@endsection
