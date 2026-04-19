FROM python:3.12-slim

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ffmpeg \
        poppler-utils \
        libreoffice \
    && rm -rf /var/lib/apt/lists/*

# LibreOffice precisa de diretório ~/.config gravável
ENV HOME=/tmp

WORKDIR /app

COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

COPY app/ ./app/

RUN mkdir -p /app/uploads /app/output /app/data

EXPOSE 9191

CMD ["uvicorn", "app.main:app", "--host", "0.0.0.0", "--port", "9191", "--workers", "1"]
