import os
import sqlite3
from datetime import datetime

DB_PATH = os.getenv("DB_PATH", "/app/data/conversions.db")


def _get_conn() -> sqlite3.Connection:
    conn = sqlite3.connect(DB_PATH, check_same_thread=False)
    conn.row_factory = sqlite3.Row
    return conn


def init_db() -> None:
    with _get_conn() as conn:
        conn.execute("""
            CREATE TABLE IF NOT EXISTS conversions (
                id               TEXT PRIMARY KEY,
                original_filename TEXT NOT NULL,
                input_format     TEXT NOT NULL,
                output_format    TEXT NOT NULL,
                conversion_type  TEXT NOT NULL,
                output_filename  TEXT,
                filesize_in      INTEGER,
                filesize_out     INTEGER,
                status           TEXT NOT NULL DEFAULT 'pending',
                error_msg        TEXT,
                created_at       TEXT NOT NULL,
                completed_at     TEXT
            )
        """)


def create_conversion(
    id: str,
    original_filename: str,
    input_format: str,
    output_format: str,
    conversion_type: str,
    filesize_in: int,
) -> None:
    with _get_conn() as conn:
        conn.execute(
            """INSERT INTO conversions
               (id, original_filename, input_format, output_format,
                conversion_type, filesize_in, status, created_at)
               VALUES (?, ?, ?, ?, ?, ?, 'pending', ?)""",
            (id, original_filename, input_format, output_format,
             conversion_type, filesize_in, datetime.now().isoformat()),
        )


def update_conversion(id: str, **kwargs) -> None:
    if not kwargs:
        return
    fields = ", ".join(f"{k} = ?" for k in kwargs)
    values = list(kwargs.values()) + [id]
    with _get_conn() as conn:
        conn.execute(f"UPDATE conversions SET {fields} WHERE id = ?", values)


def get_history(limit: int = 100) -> list[dict]:
    with _get_conn() as conn:
        rows = conn.execute(
            "SELECT * FROM conversions ORDER BY created_at DESC LIMIT ?", (limit,)
        ).fetchall()
    return [dict(r) for r in rows]


def delete_conversion_record(id: str) -> None:
    with _get_conn() as conn:
        conn.execute("DELETE FROM conversions WHERE id = ?", (id,))
