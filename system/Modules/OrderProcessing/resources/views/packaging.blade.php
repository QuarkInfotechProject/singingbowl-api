<!DOCTYPE html>
<html lang="en" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; -webkit-print-color-adjust: exact;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
    <title>Packaging Data</title>
</head>
<body style="font-family: 'Open Sans', sans-serif; font-size: 14px; min-width: 320px; color: #333333; margin: 0; padding: 20px;">
<table style="border-collapse: collapse; min-width: 320px; max-width: 600px; width: 100%; margin: auto; background-color: #ffffff; border: 1px solid #dddddd;">
    <thead>
    <tr>
        <th colspan="4" style="padding: 15px; text-align: center; font-size: 18px; font-weight: 600; border-bottom: 1px solid #dddddd;">Packaging Data</th>
    </tr>
    <tr>
        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #dddddd; font-weight: 600;">Product Name</th>
        <th style="padding: 10px; text-align: center; border-bottom: 1px solid #dddddd; font-weight: 600;">Total</th>
        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #dddddd; font-weight: 600;">Type</th>
        <th style="padding: 10px; text-align: center; border-bottom: 1px solid #dddddd; font-weight: 600;">Qty</th>
    </tr>
    </thead>
    <tbody>
    @foreach($preparedData as $productName => $productData)
        <tr>
            <td rowspan="{{ count($productData['types']) ?: 1 }}" style="padding: 10px; border-bottom: 1px solid #dddddd;">{{ $productName }}</td>
            <td rowspan="{{ count($productData['types']) ?: 1 }}" style="padding: 10px; text-align: center; border-bottom: 1px solid #dddddd;">{{ $productData['total'] }}</td>
            @if(empty($productData['types']))
                <td style="padding: 10px; border-bottom: 1px solid #dddddd;">N/A</td>
                <td style="padding: 10px; text-align: center; border-bottom: 1px solid #dddddd;">{{ $productData['total'] }}</td>
            @else
                @foreach($productData['types'] as $typeName => $typeQty)
                    @if(!$loop->first)
        </tr><tr>
            @endif
            <td style="padding: 10px; border-bottom: 1px solid #dddddd;">{{ $typeName }}</td>
            <td style="padding: 10px; text-align: center; border-bottom: 1px solid #dddddd;">{{ $typeQty }}</td>
            @endforeach
            @endif
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="4" style="padding: 10px; text-align: center; font-size: 12px; color: #666666;">Generated on {{ $date->format('D, m/d/Y, H:i:s A') }}</td>
    </tr>
    </tfoot>
</table>
</body>
</html>
