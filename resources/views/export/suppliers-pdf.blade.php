<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Suppliers Export</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            color: #333;
            padding: 10px;
        }

        .header {
            margin-bottom: 15px;
            border-bottom: 2px solid #2d5016;
            padding-bottom: 8px;
        }

        .header h1 {
            color: #2d5016;
            font-size: 16px;
            margin-bottom: 3px;
        }

        .header p {
            color: #666;
            font-size: 9px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        th {
            background-color: #2d5016;
            color: white;
            padding: 5px 4px;
            text-align: left;
            font-weight: bold;
            font-size: 8px;
            border: 1px solid #1a3a0d;
        }

        td {
            padding: 4px 3px;
            border: 1px solid #ddd;
            font-size: 8px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .supplier-name {
            font-weight: bold;
            color: #2d5016;
            background-color: #e8f0dd;
            width: 20%;
        }

        .layup-name {
            font-weight: bold;
            color: #333;
            background-color: #f0f0f0;
            width: 20%;
        }

        .footer {
            margin-top: 15px;
            text-align: right;
            font-size: 8px;
            color: #999;
        }

        @media print {
            body {
                margin: 0;
                padding: 10px;
            }
            table {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Suppliers Export</h1>
        <p>Generated on {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 20%;">Supplier Name</th>
                <th style="width: 20%;">Layup Name</th>
                <th style="width: 15%;">Layer Order</th>
                <th style="width: 15%;">Thickness</th>
                <th style="width: 15%;">Width</th>
                <th style="width: 15%;">Angle</th>
            </tr>
        </thead>
        <tbody>
            @foreach($suppliers as $supplier)
                @php
                    $isFirstSupplier = true;
                    $hasLayups = $supplier->layups->count() > 0;
                @endphp

                @if(!$hasLayups)
                    {{-- Supplier tanpa layup --}}
                    <tr>
                        <td class="supplier-name">{{ $supplier->name }}</td>
                        <td colspan="5">-</td>
                    </tr>
                @else
                    {{-- Supplier dengan layup --}}
                    @foreach($supplier->layups as $layup)
                        @php
                            $isFirstLayup = true;
                            $hasLayers = $layup->layers->count() > 0;
                        @endphp

                        @if(!$hasLayers)
                            {{-- Layup tanpa layers --}}
                            <tr>
                                <td class="supplier-name">
                                    {{ $isFirstSupplier ? $supplier->name : '' }}
                                </td>
                                <td class="layup-name">{{ $layup->name }}</td>
                                <td colspan="4">-</td>
                            </tr>
                            @php $isFirstSupplier = false; @endphp
                        @else
                            {{-- Layup dengan layers --}}
                            @foreach($layup->layers as $layer)
                                <tr>
                                    <td class="supplier-name">
                                        {{ $isFirstSupplier ? $supplier->name : '' }}
                                    </td>
                                    <td class="layup-name">
                                        {{ $isFirstLayup ? $layup->name : '' }}
                                    </td>
                                    <td>{{ $layer->layer_order ?? '-' }}</td>
                                    <td>{{ $layer->thickness ?? '-' }}</td>
                                    <td>{{ $layer->width ?? '-' }}</td>
                                    <td>{{ $layer->angle ?? '-' }}</td>
                                </tr>
                                @php
                                    $isFirstSupplier = false;
                                    $isFirstLayup = false;
                                @endphp
                            @endforeach
                        @endif
                    @endforeach
                @endif
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Page generated by CLT Supplier Management System</p>
    </div>
</body>
</html>
