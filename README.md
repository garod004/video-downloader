# Video Downloader

Interface web para baixar vídeos e músicas de qualquer plataforma, feita para rodar no servidor Umbrel.

Suporta YouTube, Twitter/X, Instagram, TikTok e centenas de outros sites via [yt-dlp](https://github.com/yt-dlp/yt-dlp).

## Funcionalidades

- Suporte a YouTube, Twitter/X, Instagram, TikTok e muito mais
- Download de vídeos em 1080p, 720p e 480p (MP4)
- Download de áudio em MP3 (192kbps)
- Suporte a playlists completas
- Progresso em tempo real com barra de andamento
- Histórico de downloads com opção de re-baixar arquivos
- Interface simples, funciona em celular e computador

## Requisitos

- Docker e Docker Compose instalados no servidor
- Conexão com internet para baixar os vídeos

## Como usar

### 1. Clonar o repositório

```bash
git clone https://github.com/garod004/video-downloader.git
cd video-downloader
```

### 2. Subir o container

```bash
docker compose up -d --build
```

### 3. Acessar no navegador

```
http://<IP-DO-UMBREL>:8090
```

Exemplo: `http://192.168.1.100:8090`

---

## Estrutura do projeto

```
.
├── app/
│   ├── main.py          # API FastAPI + rotas
│   ├── downloader.py    # Wrapper do yt-dlp
│   ├── database.py      # Histórico (SQLite)
│   └── templates/
│       └── index.html   # Interface web
├── downloads/           # Vídeos baixados (gerado automaticamente)
├── data/                # Banco SQLite (gerado automaticamente)
├── Dockerfile
├── docker-compose.yml
└── requirements.txt
```

## Atualizar o app

```bash
git pull
docker compose up -d --build
```

## Comandos úteis

```bash
# Ver logs em tempo real
docker compose logs -f

# Parar o serviço
docker compose down

# Ver arquivos baixados
ls downloads/
```

## Stack técnica

- **Backend**: Python 3.12 + FastAPI
- **Downloader**: yt-dlp (conversão via ffmpeg)
- **Banco de dados**: SQLite
- **Frontend**: HTML + CSS + Vanilla JS (sem frameworks)
- **Deploy**: Docker
