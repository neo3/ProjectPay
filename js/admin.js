$(document).ready(function () {
  $("#loadingModal").appendTo("body").modal("show");

  $('#pnlTransferencias').DataTable({
      "pageLength": 13,
      "order": [[0, 'desc']],
      "lengthChange": false,
      "autoWidth": false,
      "language": {
          "decimal": ",",
          "thousands": ".",
          "lengthMenu": "Exibindo _MENU_ ítens por página.",
          "zeroRecords": "Nenhuma transação encontrada.",
          "info": "Página _PAGE_ de _PAGES_",
          "infoEmpty": "Lista vazia.",
          "infoFiltered": "(filtrado por _MAX_)",
          "search": "Pesquisar:",
          "paginate": {
              "first": "Primeiro",
              "last": "Último",
              "next": "»",
              "previous": "«"
          }
      },
      "ajax": {
          "url": "./functions/transactions.php",
          "type": "GET",
          "dataSrc": function (json) {
              $("#loadingModal").modal("hide");
              return json;
          }
      },
      "columns": [
          { "data": "Data" },
          { "data": "Destino" },
          { "data": "Valor" },
          { "data": "Status" }
      ],
      "dom": 'rt<"card-footer col-12"<"row"<"col col-xs-4 data-add"i><"col col-xs-8"p>>><"clear">'
  });
});

function transferir() {
  
}