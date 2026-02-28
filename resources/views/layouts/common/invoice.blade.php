<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Invoice</title>
  <link rel="stylesheet" href="{{asset('assets/css/invoice.css')}}">
</head>

<body>
  <div class="invoice-box">

    <!-- Header -->
    <div class="header">
      <div class="left">
        <h2>CM Recycling Company Limited</h2>
        <p>58 St. Thomas St. Town Road<br>
          Tel: 1-876-933-5797<br>
          cmrecycling@email.com</p>
      </div>
      <div class="right">
        <h1>INVOICE</h1>
        <span class="logo"><img src="https://lab5.invoidea.work/scraperp/public/assets/images/cm-logo.png" alt=""></span>
      </div>
    </div>

    <!-- Info -->
    <div class="info">
      <div>
        <strong>Bill To:</strong><br>
        Hope & Son’s Funeral Service Ltd.<br>
        45 Old Hope Road<br>
        Kingston 6
      </div>
      <div>
        <strong>Date: </strong> 10/12/2016<br>
        <strong> Invoice #: </strong> 10508 <br>
        <strong> GCT #: </strong> 10200948 <br>


      </div>
    </div>
    <!-- Products -->
    <table>
      <tr>
        <th>Quantity</th>
        <th>Description</th>
        <th>Price Each</th>
        <th>Amount</th>
      </tr>
      <tr>
        <td>1</td>
        <td class="description">0.75 Cu Yd Tin @ 12.00</td>
        <td>12.00</td>
        <td>12.00</td>
      </tr>
      <tr>
        <td>1</td>
        <td class="description">0.75 Cu Yd Tin @ 12.00</td>
        <td>12.00</td>
        <td>12.00</td>
      </tr>

    </table>
    <!-- Totals -->
    <div class="totals">
      <table>
        <tr>
          <td>Subtotal:</td>
          <td>120.00</td>
        </tr>
        <tr>
          <td>Sales Tax (16.5%):</td>
          <td>19.80</td>
        </tr>
        <tr>
          <td><strong>Total:</strong></td>
          <td><strong>139.80</strong></td>
        </tr>
        <tr>
          <td><strong>Balance Due:</strong></td>
          <td><strong>139.80</strong></td>
        </tr>
      </table>
    </div>
    <!-- Footer -->
    <div class="footer">
      Please make payments via cheques or wire transfer to CM Recycling Co. Ltd.<br>
      National Commercial Bank<br>
      Acct# 361069188 – Portmore Branch
    </div>
  </div>
</body>

</html>