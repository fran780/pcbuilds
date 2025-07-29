<div class="container-invoice">
  {{with order}}
  {{if id}}
  <h1>Factura</h1>
  <hr />
  <section class="invoice">
    <p><strong>ID de Orden:</strong> {{id}}</p>
    <p><strong>Fecha:</strong> {{update_time}}</p>
    <p><strong>Cliente:</strong> {{payer_name}}</p>
    <p><strong>Correo:</strong> {{payer_email}}</p>
    <p><strong>Comisión PayPal:</strong> {{paypal_fee}} {{currency}}</p>
    <p><strong>Monto Neto:</strong> {{net_amount}} {{currency}}</p>
    <p><strong>Total:</strong> {{amount}} {{currency}}</p>
  </section>


  <div class="print-button">
    <button onclick="window.print()">
      <i class="fa-solid fa-print"></i> Imprimir factura
    </button>
  </div>
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


  <style>
    .container-invoice {
      max-width: 500px;
      margin: 40px auto;
      background-color: #ffffff;
      border-radius: 12px;
      padding: 30px 35px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
      font-family: "Segoe UI", sans-serif;
      color: #333;
    }

    .container-invoice h1 {
      text-align: center;
      font-size: 1.8em;
      margin-bottom: 20px;
      color: #222;
    }

    .container-invoice hr {
      margin-bottom: 20px;
    }

    .invoice p {
      margin: 10px 0;
      line-height: 1.6;
      font-size: 1.05em;
      color: rgb(23, 22, 22);
    }

    .invoice strong {
      color: #000;
    }

    .print-button {
      text-align: right;
      margin-top: 30px;
    }

    .print-button button {
      background-color: #28a745;
      color: white;
      padding: 10px 18px;
      font-size: 1em;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .print-button button:hover {
      background-color: #218838;
    }

    @media print {
      .print-button {
        display: none;
      }
    }

    .return-button {
      text-align: left;
      margin-top: 10px;
    }

    .return-button a {
      background-color: #6c757d;
      color: white;
      padding: 10px 18px;
      text-decoration: none;
      font-size: 1em;
      border-radius: 6px;
      display: inline-block;
      transition: background-color 0.3s ease;
    }

    .return-button a:hover {
      background-color: #5a6268;
    }

    @media print {
      .return-button {
        display: none;
      }
    }
  </style>