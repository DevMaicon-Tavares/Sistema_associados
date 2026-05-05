# Serviço WhatsApp Web

Este serviço Node.js gerencia o envio de mensagens via WhatsApp Web do seu navegador pessoal.

## Requisitos

- Node.js 14+
- npm ou yarn
- Ter o WhatsApp Web aberto no navegador (login realizado)

## Instalação

```bash
cd whatsapp-service
npm install
```

## Uso

### 1. Iniciar o serviço

```bash
npm start
```

Na primeira execução, será exibido um QR Code no terminal. **Abra o WhatsApp Web no seu navegador e escaneie esse código com seu celular**.

Após autenticado, você verá a mensagem:

```
✅ WhatsApp Web conectado!
```

### 2. Laravel enviará automaticamente

Quando você cadastrar uma reunião no Laravel, o sistema automaticamente enviará mensagens via WhatsApp Web para todos os associados com telefone cadastrado.

## Como funciona

1. O serviço Node.js mantém uma conexão com o WhatsApp Web
2. Laravel faz uma chamada HTTP para `http://localhost:3001/api/send-messages`
3. O serviço envia as mensagens usando seu WhatsApp Web
4. As respostas voltam com status de cada envio

## Endpoints

### POST /api/send-messages

Envia mensagens para múltiplos números.

**Body:**

```json
{
    "mensagens": [
        {
            "numero": "11999999999",
            "mensagem": "Olá, você está convidado..."
        }
    ]
}
```

**Resposta:**

```json
{
    "sucesso": true,
    "mensagens_processadas": 1,
    "detalhes": [
        {
            "numero": "11999999999",
            "status": "enviado",
            "timestamp": "2026-05-04T15:30:00.000Z"
        }
    ]
}
```

### GET /api/status

Verifica se o serviço está conectado ao WhatsApp.

**Resposta:**

```json
{
    "conectado": true,
    "status": "online"
}
```

## Troubleshooting

**Mensagem: "WhatsApp não está conectado"**

- Certifique-se de que rodou `npm start`
- Verifique se escaneou o QR Code corretamente
- Tente abrir o WhatsApp Web em novo QR Code

**Mensagens não sendo entregues**

- Verifique se o número está no formato correto (11999999999 ou com DDI)
- Certifique-se de que o número tem conversa ativa com você no WhatsApp
- Verifique os logs do terminal do serviço

**Preciso autenticar novamente?**

- A autenticação é salva em `.wwebjs_auth/`
- Se precisar resetar, delete essa pasta e rode novamente

## Notas importantes

- O serviço deve estar rodando o tempo todo para as mensagens serem enviadas
- Não feche o terminal onde o serviço está rodando
- Se precisar parar, use Ctrl+C
- O WhatsApp Web pode desconectar se você fizer login em outro local - se isso acontecer, o serviço avisa e se reconecta automaticamente
