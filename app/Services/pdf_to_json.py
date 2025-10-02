#!/usr/bin/env python3
import argparse
import json
import os
import sys

import fitz  # PyMuPDF

def extract_text_from_pdf(pdf_path):
    doc = fitz.open(pdf_path)
    pages = []
    for page_num in range(doc.page_count):
        page = doc.load_page(page_num)
        lines = []
        blocks = page.get_text("blocks")
        line_number = 1
        for block in blocks:
            text = block[4].strip()
            if text:
                for line in text.splitlines():
                    if line.strip():
                        lines.append({
                            "line": line_number,
                            "text": line.strip()
                        })
                        line_number += 1
        pages.append({
            "page": page_num + 1,
            "lines": lines
        })
    return pages

def extract_metadata(pdf_path):
    doc = fitz.open(pdf_path)
    meta = doc.metadata
    # Use common PDF metadata fields
    return {
        "judul": meta.get("title"),
        "penerbit": meta.get("publisher") or meta.get("author"),
        "tahun": meta.get("creationDate", "")[2:6] if meta.get("creationDate") else None,
        "edisi": None  # Usually not in PDF metadata
    }

def main():
    ap = argparse.ArgumentParser(description="PDF -> JSON (pages + lines) using PyMuPDF")
    ap.add_argument("pdf", help="Path to input PDF file")
    ap.add_argument("-o", "--output", help="Path to output JSON (default: input.json beside PDF)")
    ap.add_argument("--judul", help="Override judul (title)")
    ap.add_argument("--penerbit", help="Override penerbit (publisher)")
    ap.add_argument("--tahun", type=int, help="Override tahun (year)")
    ap.add_argument("--edisi", help="Override edisi (edition)")
    args = ap.parse_args()

    if not os.path.isfile(args.pdf) or not args.pdf.lower().endswith(".pdf"):
        print("Input must be a .pdf file", file=sys.stderr)
        sys.exit(2)

    out_path = args.output or (os.path.splitext(args.pdf)[0] + ".json")
    meta = extract_metadata(args.pdf)
    pages = extract_text_from_pdf(args.pdf)
    payload = {
        "judul": args.judul or meta["judul"],
        "penerbit": args.penerbit or meta["penerbit"],
        "tahun": args.tahun or meta["tahun"],
        "edisi": args.edisi or meta["edisi"],
        "pages": pages
    }

    with open(out_path, "w", encoding="utf-8") as f:
        json.dump(payload, f, ensure_ascii=False, indent=2)

    print(f"OK: wrote {out_path}")

if __name__ == "__main__":
    main()