const express = require('express');
const { Client, LocalAuth } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');
require('dotenv').config();

const app = express();
app.use(express.json());

let client;
let isReady = false;

// Inicializar cliente WhatsApp
client = new Client({
    authStrategy: new LocalAuth({
        clientId: "sistema-associados"
    })
});

// QR Code para autenticação
client.on('qr', (qr) => {
    console.log('QR Code recebido, escaneie com seu WhatsApp:');
    qrcode.generate(qr, { small: true });
});

// Quando conecta
client.on('ready', () => {
    console.log('✅ WhatsApp Web conectado!');
    isReady = true;
});

// Quando desconecta
client.on('disconnected', () => {
    console.log('❌ WhatsApp Web desconectado!');
    isReady = false;
});

client.on('error', (error) => {
    console.error('Erro no WhatsApp:', error);
});

client.initialize();

// Endpoint para enviar mensagens
app.post('/api/send-messages', async (req, res) => {
    if (!isReady) {
        return res.status(400).json({ error: 'WhatsApp não está conectado' });
    }

    const { mensagens } = req.body; // Array de {numero, mensagem}

    if (!Array.isArray(mensagens) || mensagens.length === 0) {
        return res.status(400).json({ error: 'Nenhuma mensagem para enviar' });
    }

    const resultados = [];

    for (const item of mensagens) {
        try {
            // Formata o número para formato WhatsApp
            let numero = item.numero.toString().replace(/\D/g, ''); // Remove tudo que não é número
            
            // Se não começar com +55, adiciona (assumindo Brasil)
            if (!numero.startsWith('55')) {
                if (numero.startsWith('0')) {
                    numero = numero.substring(1); // Remove o 0 inicial
                }
                numero = '55' + numero;
            }
            
            // Remove o + se tiver
            numero = numero.replace('+', '');
            
            console.log(`📱 Formatando: ${item.numero} → ${numero}`);
            
            const chatId = numero + '@c.us';
            
            // Tenta verificar se o contato existe usando getNumberId
            try {
                const numberDetails = await client.getNumberId(numero);
                if (!numberDetails) {
                    throw new Error('Contato não encontrado no WhatsApp');
                }
            } catch (checkError) {
                console.warn(`⚠️ Contato ${numero} pode não estar sincronizado: ${checkError.message}`);
                // Continua mesmo assim, alguns contatos podem estar em chats sem estar nos contatos
            }
            
            // Tenta enviar a mensagem
            await client.sendMessage(chatId, item.mensagem);
            
            resultados.push({
                numero: item.numero,
                numero_formatado: numero,
                status: 'enviado',
                timestamp: new Date()
            });
            
            console.log(`✓ Mensagem enviada para ${item.numero} (${numero})`);
        } catch (error) {
            resultados.push({
                numero: item.numero,
                status: 'erro',
                erro: error.message,
                dica: 'Certifique-se de que o contato está salvo no WhatsApp Web'
            });
            
            console.error(`✗ Erro ao enviar para ${item.numero}: ${error.message}`);
        }
    }

    res.json({
        sucesso: true,
        mensagens_processadas: resultados.length,
        detalhes: resultados
    });
});

// Endpoint para verificar se um número existe no WhatsApp
app.post('/api/check-number', async (req, res) => {
    if (!isReady) {
        return res.status(400).json({ error: 'WhatsApp não está conectado' });
    }

    const { numero } = req.body;

    if (!numero) {
        return res.status(400).json({ error: 'Número não fornecido' });
    }

    try {
        // Formata o número
        let numeroFormatado = numero.toString().replace(/\D/g, '');
        
        if (!numeroFormatado.startsWith('55')) {
            if (numeroFormatado.startsWith('0')) {
                numeroFormatado = numeroFormatado.substring(1);
            }
            numeroFormatado = '55' + numeroFormatado;
        }

        const numberDetails = await client.getNumberId(numeroFormatado);
        
        if (numberDetails) {
            res.json({
                existe: true,
                numero: numero,
                numero_formatado: numeroFormatado,
                status: 'encontrado'
            });
        } else {
            res.json({
                existe: false,
                numero: numero,
                numero_formatado: numeroFormatado,
                status: 'não encontrado',
                dica: 'Adicione este número aos contatos do seu WhatsApp e sincronize'
            });
        }
    } catch (error) {
        res.json({
            existe: false,
            numero: numero,
            status: 'erro',
            erro: error.message,
            dica: 'Certifique-se de que o número está em formato correto'
        });
    }
});

// Endpoint para verificar status
app.get('/api/status', (req, res) => {
    res.json({
        conectado: isReady,
        status: isReady ? 'online' : 'offline'
    });
});

// Iniciar servidor
const PORT = process.env.PORT || 3001;
app.listen(PORT, () => {
    console.log(`🚀 Serviço WhatsApp rodando em http://localhost:${PORT}`);
});
