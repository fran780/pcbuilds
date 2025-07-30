<div class="container-invoice">
  {{with order}}
  {{if id}}
  <div class="logo-img">
      <!-- Espacio para el logo de la empresa -->
      <img src="public/imgs/hero/PC Builds Main.png" alt="Logo" class="company-logo" />
    </div>
  <div class="invoice-header">
    <div class="logo-icon">
      <i class="fa-solid fa-file-invoice-dollar"></i>
    </div>
    <div>
      <h1>Factura</h1>
      <span class="invoice-id">#{{id}}</span>
    </div>
  </div>
  <hr />
  <section class="invoice">
    <div class="invoice-row">
      <span class="label"><i class="fa-solid fa-calendar-day"></i> Fecha:</span>
      <span>{{update_time}}</span>
    </div>
    <div class="invoice-row">
      <span class="label"><i class="fa-solid fa-user"></i> Cliente:</span>
      <span>{{payer_name}}</span>
    </div>
    <div class="invoice-row">
      <span class="label"><i class="fa-solid fa-envelope"></i> Correo:</span>
      <span>{{payer_email}}</span>
    </div>
    <div class="invoice-row">
      <span class="label"><i class="fa-brands fa-paypal"></i> Comisión PayPal:</span>
      <span>{{paypal_fee}} {{currency}}</span>
    </div>
    <div class="invoice-row">
      <span class="label"><i class="fa-solid fa-money-bill-wave"></i> Monto Neto:</span>
      <span>{{net_amount}} {{currency}}</span>
    </div>
    <div class="invoice-row total-row">
      <span class="label"><i class="fa-solid fa-coins"></i> Total:</span>
      <span>{{amount}} {{currency}}</span>
    </div>
  </section>

  <div class="print-button">
    <button onclick="window.print()">
      <i class="fa-solid fa-print"></i> Imprimir factura
    </button>
  </div>
  {{endif id}}
  {{endwith order}}
</div>

<div class="buttons-row">
  <div class="return-button">
    <a href="index.php">
      <i class="fa-solid fa-house"></i> Volver al inicio
    </a>
  </div>
</div>

<style>
  body {
    background: linear-gradient(135deg, #e9ecef 0%, #f8f9fa 100%);
    min-height: 100vh;
  }

  .container-invoice {
    max-width: 480px;
    margin: 40px auto;
    background: #fff;
    border-radius: 16px;
    padding: 32px 38px 24px 38px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.10);
    font-family: "Segoe UI", sans-serif;
    color: #333;
    position: relative;
  }

  .invoice-header {
    display: flex;
    align-items: center;
    gap: 18px;
    margin-bottom: 10px;
  }

  .logo-img {
    width: 64px;
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 8px;
  }

  .company-logo {
    max-width: 60px;
    max-height: 60px;
    border-radius: 8px;
    object-fit: contain;
    background: #f8f9fa;
    box-shadow: 0 2px 8px rgba(40,167,69,0.08);
  }

  .logo-icon {
    font-size: 2.5em;
    color: #28a745;
    background: #e9f7ef;
    border-radius: 50%;
    padding: 12px;
    box-shadow: 0 2px 8px rgba(40,167,69,0.08);
  }

  .invoice-header h1 {
    margin: 0;
    font-size: 2em;
    color: #222;
    font-weight: 600;
    letter-spacing: 1px;
  }

  .invoice-id {
    font-size: 0.95em;
    color: #6c757d;
    font-weight: 500;
    margin-left: 2px;
  }

  .container-invoice hr {
    margin: 18px 0 22px 0;
    border: none;
    border-top: 1.5px solid #e9ecef;
  }

  .invoice {
    margin-bottom: 10px;
  }

  .invoice-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 12px 0;
    font-size: 1.08em;
    padding-bottom: 4px;
    border-bottom: 1px dashed #f1f3f4;
  }

  .invoice-row:last-child {
    border-bottom: none;
  }

  .invoice-row .label {
    color: #28a745;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .total-row {
    font-size: 1.18em;
    font-weight: 600;
    color: #218838;
    background: #f8f9fa;
    border-radius: 6px;
    margin-top: 18px;
    padding: 10px 0;
    border-bottom: none;
  }

  .print-button {
    text-align: right;
    margin-top: 30px;
  }

  .print-button button {
    background: linear-gradient(90deg, #28a745 60%, #218838 100%);
    color: white;
    padding: 12px 22px;
    font-size: 1.08em;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(40,167,69,0.08);
    transition: background 0.3s;
    font-weight: 500;
  }

  .print-button button:hover {
    background: linear-gradient(90deg, #218838 60%, #28a745 100%);
  }

  @media print {
    body {
      background: #fff !important;
      margin: 0 !important;
      padding: 0 !important;
    }
    .container-invoice {
      box-shadow: none !important;
      border: none !important;
      max-width: 100% !important;
      padding: 0 24px !important;
      margin: 0 !important;
      color: #222 !important;
    }
    .print-button, .return-button, .buttons-row {
      display: none !important;
    }
    .invoice-header {
      margin-bottom: 18px !important;
    }
    .invoice-row {
      font-size: 1em !important;
      border-bottom: 1px solid #e9ecef !important;
      padding-bottom: 6px !important;
    }
    .total-row {
      background: #e9f7ef !important;
      color: #218838 !important;
      font-size: 1.15em !important;
      border-radius: 0 !important;
      margin-top: 18px !important;
      padding: 12px 0 !important;
    }
    .logo-img, .logo-icon {
      background: none !important;
      box-shadow: none !important;
      padding: 0 !important;
    }
    .company-logo {
      background: none !important;
      box-shadow: none !important;
    }
  }

  .buttons-row {
    max-width: 480px;
    margin: 0 auto;
    margin-top: 18px;
    display: flex;
    justify-content: flex-start;
  }

  .return-button {
    text-align: left;
  }

  .return-button a {
    background: linear-gradient(90deg, #6c757d 60%, #5a6268 100%);
    color: white;
    padding: 10px 20px;
    text-decoration: none;
    font-size: 1em;
    border-radius: 8px;
    display: inline-block;
    transition: background 0.3s;
    font-weight: 500;
    box-shadow: 0 2px 8px rgba(108,117,125,0.08);
  }

  .return-button a:hover {
    background: linear-gradient(90deg, #5a6268 60%, #6c757d 100%);
  }
</style>